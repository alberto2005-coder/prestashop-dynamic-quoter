<div class="card mt-2">
    <div class="card-header">
        <h3 class="card-header-title">
            <i class="icon-cogs"></i> {l s='Custom Order Parameters' d='Modules.Dynamicprice.Admin'}
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Product ID' d='Modules.Dynamicprice.Admin'}</th>
                        <th>{l s='Width' d='Modules.Dynamicprice.Admin'}</th>
                        <th>{l s='Height' d='Modules.Dynamicprice.Admin'}</th>
                        <th>{l s='Material' d='Modules.Dynamicprice.Admin'}</th>
                        <th>{l s='Density' d='Modules.Dynamicprice.Admin'}</th>
                        <th>{l s='Custom Price' d='Modules.Dynamicprice.Admin'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$custom_data item=item}
                        <tr>
                            <td>{$item.id_product|intval}</td>
                            <td>{$item.width|floatval} mm</td>
                            <td>{$item.height|floatval} mm</td>
                            <td>{$item.material|escape:'html':'UTF-8'|upper}</td>
                            <td>{$item.density|intval}%</td>
                            <td>{displayPrice price=$item.price}</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</div>
