<div id="dynamic-price-calculator" class="product-custom-fields box-info-product">
    <h3>{l s='Custom Order Parameters' mod='dynamicprice'}</h3>
    <div class="row">
        <div class="col-md-6 form-group">
            <label for="dp_width">{l s='Width (mm)' mod='dynamicprice'}</label>
            <input type="number" name="dp_width" id="dp_width" class="form-control dp-calc-field" value="100" min="1">
        </div>
        <div class="col-md-6 form-group">
            <label for="dp_height">{l s='Height (mm)' mod='dynamicprice'}</label>
            <input type="number" name="dp_height" id="dp_height" class="form-control dp-calc-field" value="100" min="1">
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-6 form-group">
            <label for="dp_material">{l s='Material Type' mod='dynamicprice'}</label>
            <select name="dp_material" id="dp_material" class="form-control dp-calc-field">
                <option value="pla">{l s='PLA Standard' mod='dynamicprice'}</option>
                <option value="abs">{l s='ABS Durable' mod='dynamicprice'}</option>
                <option value="petg">{l s='PETG Tough' mod='dynamicprice'}</option>
            </select>
        </div>
        <div class="col-md-6 form-group">
            <label for="dp_density">{l s='Infill Density (%)' mod='dynamicprice'}</label>
            <input type="number" name="dp_density" id="dp_density" class="form-control dp-calc-field" value="20" min="0" max="100">
        </div>
    </div>
    <div class="dynamic-price-display mt-3">
        <strong>{l s='Estimated Price:' mod='dynamicprice'} </strong>
        <span id="computed-price-display">--</span>
    </div>
    
    <input type="hidden" name="custom_dynamic_price" id="custom_dynamic_price" value="">
</div>

<style>
#dynamic-price-calculator {
    padding: 15px;
    border: 1px solid #ddd;
    margin-bottom: 20px;
    background: #f9f9f9;
    border-radius: 8px;
}
.dp-calc-field {
    margin-bottom: 10px;
}
#computed-price-display {
    font-size: 1.2em;
    color: #2fb5d2;
}
</style>
