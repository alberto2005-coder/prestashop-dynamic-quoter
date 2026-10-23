# Dynamic 3D Quoter para PrestaShop 🛒

Un módulo avanzado para PrestaShop (v1.7 / 8.x / 9.x) que permite calcular y modificar el precio de un producto en tiempo real basándose en parámetros variables introducidos por el usuario. 

Diseñado específicamente para negocios de fabricación bajo demanda, permitiendo cotizar impresiones 3D directamente desde la ficha de producto con previsualización visual.

## 🚀 Características Principales

* **Cálculo dinámico por AJAX:** Actualización del precio en el frontend sin recargas de página.
* **Interfaz Premium:** Sliders interactivos para dimensiones y densidad.
* **Previsualización Dinámica:** Recuadro visual que escala en tiempo real según las dimensiones introducidas.
* **Múltiples Materiales:** Configuración de multiplicadores para PLA, ABS y PETG.
* **Persistencia de Datos:** Los parámetros personalizados se guardan en la base de datos y son visibles en el pedido (Backoffice y Frontend).
* **Sobreescritura segura:** Utiliza el sistema de overrides de PrestaShop para inyectar el precio personalizado.
* **Seguridad y Validación Reforzada:** Protección CSRF timing-safe, validación estricta de límites (densidad 0-100%, dimensiones finitas) y lista blanca de materiales.

## 📊 Matriz de Compatibilidad (PrestaShop & PHP)

> **Nota sobre el soporte de PHP:** PHP 8.0 y versiones anteriores han alcanzado su fin de ciclo de vida (EOL) y ya no reciben soporte oficial de seguridad. Se recomienda encarecidamente utilizar versiones con soporte activo (**PHP 8.1, 8.2 o 8.3**).

| Versión PrestaShop | Ciclo de Vida PrestaShop | Versiones PHP Compatibles | Versión PHP Recomendada | Estado del Módulo |
| :--- | :--- | :--- | :--- | :--- |
| **PrestaShop 9.0.x** | Activo (Próxima gen) | PHP 8.1, 8.2, 8.3, 8.4 | PHP 8.2 / 8.3 | Compatible (Forward Ready) |
| **PrestaShop 8.2.x** | Soporte Activo | PHP 8.1, 8.2, 8.3 | PHP 8.2 / 8.3 | ✅ Soporte Oficial |
| **PrestaShop 8.1.x** | Soporte Activo (LTS) | PHP 8.1, 8.2 | PHP 8.1 / 8.2 | ✅ Soporte Oficial |
| **PrestaShop 1.7.8.x** | EOL (Fin de Soporte) | PHP 7.4 *(PS 1.7 no soporta PHP 8.1+)* | PHP 7.4 (Legacy) | Modo Legacy / Compatibilidad |

## 🛠️ Tecnologías Utilizadas

* **Backend:** PHP 8.1+ (compatible con PHP 8.2, 8.3 y 8.4; PHP 7.4 en modo legacy), Arquitectura MVC de PrestaShop, MySQL.
* **Frontend:** JavaScript (ES6+), jQuery, CSS3 (Modern UI).

## 🔒 Auditoría de Seguridad y Parches Aplicados

1. **Prevención de Bypass de Precios:** Validación estricta de rangos para infill/densidad (0% - 100%) y verificación de números finitos para dimensiones, evitando manipulación o precios nulos/negativos.
2. **Protección CSRF y Timing Attacks:** Validación de tokens mediante comparación segura en tiempo constante (`hash_equals`).
3. **Lista Blanca de Materiales:** Validación estricta de los materiales permitidos (`pla`, `abs`, `petg`).
4. **Protección XSS:** Escapado riguroso de variables en plantillas Smarty tanto en Back Office como Front Office.
5. **Compatibilidad PHP 8.4+:** Eliminación de advertencias de tipo nulo implícito (`?Context $context = null` en `Product.php`).

## ⚙️ Instalación

1. Descarga el repositorio como un archivo `.zip`.
2. En el panel de administración de PrestaShop, ve a "Módulos > Gestor de Módulos > Subir un módulo".
3. Selecciona el archivo `.zip`.
4. Configura los costes base y multiplicadores desde la página de configuración del módulo.

## 💡 Flujo de trabajo (Arquitectura)

1. El usuario ajusta los parámetros en `product_fields.tpl`.
2. `front.js` detecta el cambio, actualiza la previsualización y envía una petición AJAX al controlador `ajax.php`.
3. El controlador valida los datos, aplica las fórmulas y devuelve el precio formateado.
4. El precio se guarda en la cookie y en la tabla `ps_dynamicprice_info`.
5. Al añadir al carrito, el override en `Cart.php` aplica el coste dinámico.

## 🧪 Pruebas Automatizadas

El proyecto incluye una suite de pruebas para verificar la compatibilidad de versiones PHP y las reglas de seguridad:

```bash
php tests/test_compatibility_and_security.php
```

---

# English Version

**Dynamic 3D Quoter for PrestaShop** 🛒

An advanced module for PrestaShop (v1.7 / 8.x / 9.x) that calculates and modifies a product's price in real time based on variable parameters entered by the user.

Designed specifically for made-to-order manufacturing businesses, it allows quoting 3D prints directly from the product page with a visual preview.

## 🚀 Key Features

* **Dynamic AJAX calculation:** Price updates on the frontend without page reloads.
* **Premium interface:** Interactive sliders for dimensions and density.
* **Dynamic Preview:** A visual box that scales in real time according to the entered dimensions.
* **Multiple Materials:** Configurable multipliers for PLA, ABS and PETG.
* **Data Persistence:** Custom parameters are stored in the database and visible in the order (Backoffice and Frontend).
* **Safe Overriding:** Uses PrestaShop's override system to inject the customized price.
* **Hardened Security & Validation:** Timing-safe CSRF protection, strict boundary validation (density 0-100%, finite dimensions), and material whitelisting.

## 📊 Compatibility Matrix (PrestaShop & PHP)

> **Note on PHP Support:** PHP 8.0 and earlier have reached End-of-Life (EOL) and no longer receive security maintenance. Using actively supported PHP versions (**PHP 8.1, 8.2, or 8.3**) is strongly recommended.

| PrestaShop Version | PrestaShop Lifecycle | Supported PHP Versions | Recommended PHP Version | Module Status |
| :--- | :--- | :--- | :--- | :--- |
| **PrestaShop 9.0.x** | Active (Next Gen) | PHP 8.1, 8.2, 8.3, 8.4 | PHP 8.2 / 8.3 | Compatible (Forward Ready) |
| **PrestaShop 8.2.x** | Active Support | PHP 8.1, 8.2, 8.3 | PHP 8.2 / 8.3 | ✅ Officially Supported |
| **PrestaShop 8.1.x** | Active Support (LTS) | PHP 8.1, 8.2 | PHP 8.1 / 8.2 | ✅ Officially Supported |
| **PrestaShop 1.7.8.x** | EOL (End of Life) | PHP 7.4 *(PS 1.7 does not support PHP 8.1+)* | PHP 7.4 (Legacy) | Legacy Mode / Compatible |

## 🛠️ Technologies Used

* **Backend:** PHP 8.1+ (compatible with PHP 8.2, 8.3, and 8.4; PHP 7.4 in legacy mode), PrestaShop MVC architecture, MySQL.
* **Frontend:** JavaScript (ES6+), jQuery, CSS3 (Modern UI).

## 🔒 Security Audit & Applied Fixes

1. **Price Manipulation Bypass Prevention:** Strict bounding on infill density (0% - 100%) and finite float checking on dimensions, preventing zero-price or negative pricing attacks.
2. **CSRF & Timing Attack Protection:** Token verification using constant-time comparison (`hash_equals`).
3. **Material Whitelist:** Whitelist enforcement on material options (`pla`, `abs`, `petg`).
4. **XSS Protection:** Context-aware Smarty variable escaping across Front Office and Back Office order detail views.
5. **PHP 8.4+ Compatibility:** Fixed implicit nullable parameter warning (`?Context $context = null` in `Product.php`).

## ⚙️ Installation

1. Download the repository as a `.zip` file.
2. In the PrestaShop admin panel, go to "Modules > Module Manager > Upload a module".
3. Select the `.zip` file.
4. Configure the base costs and multipliers from the module's configuration page.

## 💡 Workflow (Architecture)

1. The user adjusts the parameters in `product_fields.tpl`.
2. `front.js` detects the change, updates the preview and sends an AJAX request to the `ajax.php` controller.
3. The controller validates the data, applies the formulas and returns the formatted price.
4. The price is stored in the cookie and in the `ps_dynamicprice_info` table.
5. When adding to the cart, the override in `Cart.php` applies the dynamic cost.

## 🧪 Automated Testing

The repository provides a test suite to verify PHP compatibility and security validation rules:

```bash
php tests/test_compatibility_and_security.php
```
