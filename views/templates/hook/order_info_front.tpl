<div class="box mt-3">
    <h4>{l s='Custom Dimensions for your order' d='Modules.Dynamicprice.Shop'}</h4>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{l s='Product' d='Modules.Dynamicprice.Shop'}</th>
                    <th>{l s='Configuration' d='Modules.Dynamicprice.Shop'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$custom_data item=item}
                    <tr>
                        <td>{l s='Product ID:' d='Modules.Dynamicprice.Shop'} {$item.id_product}</td>
                        <td>
                            <strong>{l s='Size:' d='Modules.Dynamicprice.Shop'}</strong> {$item.width}x{$item.height}mm<br>
                            <strong>{l s='Material:' d='Modules.Dynamicprice.Shop'}</strong> {$item.material|upper}<br>
                            <strong>{l s='Density:' d='Modules.Dynamicprice.Shop'}</strong> {$item.density}%
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>
