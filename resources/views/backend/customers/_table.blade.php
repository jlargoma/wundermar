<table class="table table-hover demo-table-search table-responsive-block" id="tableWithSearch">
    <thead>
        <tr>
            <th class ="text-center hidden bg-complete text-white">id          </th>
            <th class ="text-center bg-complete text-white" >       Nombre      </th>
            <th class ="text-center bg-complete text-white" >       Email       </th>
            <th class ="text-center bg-complete text-white" >       Telefono    </th>
            <th class ="text-center bg-complete text-white" >       Temporada </th>
            <th class ="text-center bg-complete text-white">Acción</th>
            
        </tr>
    </thead>
    <tbody>
        <?php foreach ($customers as $customer): ?>
            <tr id="customer-<?php echo $customer->id ?>">
                <td class="text-center font-s16 hidden" >
                    <?php echo $customer->id ?>
                </td>
                <td class="text-center font-s16">
                   <?php  echo $customer->name?>
                </td>
                <td class="text-center font-s16">
                    <?php  echo $customer->email?>
                </td>
                <td class="text-center font-s16">
                    <?php  echo $customer->phone?>
                </td>
                <td class="text-center font-s16">
                   <?php  echo $customer->seasson?>
                </td>
                <td class="text-justify font-s16">
                    <button class="btn btn-danger btn-xs deleteCustomer" type="button" data-id="<?php echo $customer->id ?>">
                       <i class="fa fa-close"></i>
                   </button>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>