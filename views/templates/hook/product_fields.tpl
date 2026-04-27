<div id="dynamic-price-calculator" class="product-custom-fields box-info-product">
    <h3>{l s='Custom Order Parameters' d='Modules.Dynamicprice.Shop'}</h3>
    <div class="row">
        <div class="col-md-6 form-group">
            <label for="dp_width">{l s='Width (mm)' d='Modules.Dynamicprice.Shop'}: <span id="dp_width_val">100</span>mm</label>
            <input type="range" id="dp_width_range" class="custom-range" min="1" max="500" value="100">
            <input type="hidden" name="dp_width" id="dp_width" class="dp-calc-field" value="100">
        </div>
        <div class="col-md-6 form-group">
            <label for="dp_height">{l s='Height (mm)' d='Modules.Dynamicprice.Shop'}: <span id="dp_height_val">100</span>mm</label>
            <input type="range" id="dp_height_range" class="custom-range" min="1" max="500" value="100">
            <input type="hidden" name="dp_height" id="dp_height" class="dp-calc-field" value="100">
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-6 form-group">
            <label for="dp_material">{l s='Material Type' d='Modules.Dynamicprice.Shop'}</label>
            <select name="dp_material" id="dp_material" class="form-control dp-calc-field">
                <option value="pla">{l s='PLA Standard' d='Modules.Dynamicprice.Shop'}</option>
                <option value="abs">{l s='ABS Durable' d='Modules.Dynamicprice.Shop'}</option>
                <option value="petg">{l s='PETG Tough' d='Modules.Dynamicprice.Shop'}</option>
            </select>
        </div>
        <div class="col-md-6 form-group">
            <label for="dp_density">{l s='Infill Density (%)' d='Modules.Dynamicprice.Shop'}: <span id="dp_density_val">20</span>%</label>
            <input type="range" id="dp_density_range" class="custom-range" min="0" max="100" value="20">
            <input type="hidden" name="dp_density" id="dp_density" class="dp-calc-field" value="20">
        </div>
    </div>

    <div class="preview-section mt-4 mb-4 text-center">
        <h5>{l s='3D Print Preview' d='Modules.Dynamicprice.Shop'}</h5>
        <div class="preview-canvas-wrapper">
            <div id="dynamic-preview-box"></div>
        </div>
    </div>

    <div class="dynamic-price-display mt-3">
        <strong>{l s='Estimated Price:' d='Modules.Dynamicprice.Shop'} </strong>
        <div class="price-container">
            <span id="computed-price-display">--</span>
            <div id="dp-loader" class="spinner-border spinner-border-sm text-info ml-2" role="status" style="display: none;"></div>
        </div>
    </div>
    
    <input type="hidden" name="custom_dynamic_price" id="custom_dynamic_price" value="">
</div>

<style>
#dynamic-price-calculator {
    padding: 25px;
    border: none;
    margin-bottom: 20px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.preview-canvas-wrapper {
    background: #f1f5f9;
    padding: 40px;
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 200px;
    border: 1px dashed #cbd5e1;
}
#dynamic-preview-box {
    background: #63b3ed;
    border: 2px solid #3182ce;
    border-radius: 4px;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}
.custom-range {
    width: 100%;
    height: 1.4rem;
    padding: 0;
    background-color: transparent;
    appearance: none;
}
.price-container {
    display: flex;
    align-items: center;
}
#computed-price-display {
    font-size: 1.4rem;
    color: #63b3ed;
    font-weight: bold;
}
</style>
