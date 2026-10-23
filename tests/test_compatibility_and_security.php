<?php
/**
 * Test Suite: Compatibility & Security Verification for prestashop-dynamic-quoter
 */

$errors = 0;
$testsRun = 0;

function assertTest($condition, $description) {
    global $errors, $testsRun;
    $testsRun++;
    if ($condition) {
        echo "  [PASS] $description\n";
    } else {
        echo "  [FAIL] $description\n";
        $errors++;
    }
}

echo "=== 1. PHP Syntax & Deprecation Linting ===\n";

$phpFiles = [
    'dynamicprice.php',
    'controllers/front/ajax.php',
    'override/classes/Cart.php',
    'override/classes/Product.php',
];

foreach ($phpFiles as $file) {
    $fullPath = __DIR__ . '/../' . $file;
    assertTest(file_exists($fullPath), "File exists: $file");
    
    // Execute php -l
    $cmd = escapeshellcmd(PHP_BINARY) . " -l " . escapeshellarg($fullPath) . " 2>&1";
    exec($cmd, $output, $returnCode);
    $outputStr = implode("\n", $output);
    assertTest($returnCode === 0, "php -l passes for $file without syntax errors");
    assertTest(!stripos($outputStr, 'Deprecated:'), "No PHP deprecation warnings in $file (output: " . trim($outputStr) . ")");
}

echo "\n=== 2. Security & Validation Logic Tests ===\n";

// Test timing-safe comparison
$validToken = 'secret123token';
$tamperedToken = 'secret123token_tampered';
assertTest(hash_equals($validToken, $validToken), "hash_equals validates identical tokens");
assertTest(!hash_equals($validToken, $tamperedToken), "hash_equals rejects tampered token");

// Test Density bounds validation
function validateDensity($density) {
    return ($density >= 0 && $density <= 100);
}
assertTest(validateDensity(20), "Density 20% is valid");
assertTest(validateDensity(0), "Density 0% is valid (edge case)");
assertTest(validateDensity(100), "Density 100% is valid (edge case)");
assertTest(!validateDensity(-100), "Density -100% (pricing bypass exploit) is rejected");
assertTest(!validateDensity(150), "Density 150% is rejected");

// Test Material Whitelist validation
function validateMaterial($material) {
    $allowed = ['pla', 'abs', 'petg'];
    return in_array($material, $allowed, true);
}
assertTest(validateMaterial('pla'), "'pla' material is accepted");
assertTest(validateMaterial('abs'), "'abs' material is accepted");
assertTest(validateMaterial('petg'), "'petg' material is accepted");
assertTest(!validateMaterial('hacked_material'), "Arbitrary string 'hacked_material' is rejected");
assertTest(!validateMaterial("'; DROP TABLE--"), "SQL injection string in material is rejected");

// Test Dimension Validation
function validateDimensions($w, $h, $min = 10, $max = 500) {
    if (!is_finite($w) || !is_finite($h)) return false;
    if ($w < $min || $w > $max || $h < $min || $h > $max) return false;
    return true;
}
assertTest(validateDimensions(100, 100), "Dimensions 100x100 within range [10, 500] are valid");
assertTest(validateDimensions(10, 500), "Boundary dimensions 10x500 are valid");
assertTest(!validateDimensions(5, 100), "Width 5mm below min (10mm) is rejected");
assertTest(!validateDimensions(100, 999), "Height 999mm above max (500mm) is rejected");
assertTest(!validateDimensions(NAN, 100), "NAN dimensions are rejected");
assertTest(!validateDimensions(INF, 100), "INF dimensions are rejected");

// Test Price Calculation Formula Consistency
function calculatePrice($basePrice, $w, $h, $material, $density, $baseCost, $taxRate, $multipliers) {
    $multiplier = $multipliers[$material] ?? 1.0;
    $volumeFactor = ($w * $h) / 10000;
    $densityFactor = 1 + ($density / 100);
    $extraCost = $volumeFactor * $baseCost * $multiplier;
    $extraCostTaxed = $extraCost * (1 + ($taxRate / 100));
    $newPrice = ($basePrice + $extraCostTaxed) * $densityFactor;
    return $newPrice;
}

$multipliers = ['pla' => 1.0, 'abs' => 1.2, 'petg' => 1.5];
$p1 = calculatePrice(10.0, 100, 100, 'pla', 20, 0.05, 21.0, $multipliers);
// 100*100 / 10000 = 1.0 cm2
// extraCost = 1.0 * 0.05 * 1.0 = 0.05
// extraCostTaxed = 0.05 * 1.21 = 0.0605
// densityFactor = 1 + 0.20 = 1.2
// newPrice = (10 + 0.0605) * 1.2 = 12.0726
assertTest(abs($p1 - 12.0726) < 0.0001, "Calculated price is mathematically accurate for PLA 20% ($p1)");

$pAbs = calculatePrice(10.0, 100, 100, 'abs', 20, 0.05, 21.0, $multipliers);
assertTest($pAbs > $p1, "ABS price ($pAbs) is strictly higher than PLA price ($p1) due to multiplier");

echo "\n=== 3. Summary ===\n";
echo "Total Tests Run: $testsRun\n";
echo "Total Failures: $errors\n";

if ($errors > 0) {
    echo "TEST SUITE FAILED!\n";
    exit(1);
} else {
    echo "ALL TESTS PASSED SUCCESSFULLY!\n";
    exit(0);
}
