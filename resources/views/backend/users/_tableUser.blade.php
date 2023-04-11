<table class="table table-hover table-condensed">
    <thead>
    <tr>
        <th class="text-center bg-complete text-white" style="width: 25%">#</th>
        <th class="text-center bg-complete text-white" style="width: 25%">Nombre</th>
        <th class="text-center bg-complete text-white" style="width: 35%">Email</th>
        <th class="text-center bg-complete text-white" style="width: 25%">Telefono</th>
        <th class="text-center bg-complete text-white" style="width: 15%">Tipo</th>
        <th class="text-center bg-complete text-white" style="width: 25%">Modificar</th>

    </tr>
    </thead>
    <tbody>
	<?php foreach ($users as $key => $user): ?>
    <tr>
        <td class="text-center">
			<?php echo $key + 1; ?>
        </td>
        <td class="text-center "><?php echo $user->name ?></td>
        <td class="text-center ">
		    <?php echo $user->email ?>
        </td>
        <td class="text-center "><a href="tel:<?php echo $user->phone ?>"><?php echo $user->phone ?></a></td>
        <td class="text-center ">
            <span class="label 
                <?php if($user->role == "admin") echo 'label-inverse'; ?>
                <?php if($user->role == "subadmin") echo 'label-success'; ?>
                <?php if($user->role == "propietario") echo 'label-warning'; ?>
              "><?php echo strtoupper($user->role) ?></span>
        </td>

        <td class="text-center">
            <div class="btn-group">
                <!--  -->
                <button class="btn btn-tag btn-complete update-user" type="button"
                        data-id="<?php
				        echo $user->id ?>" data-toggle="modal" data-target="#updateUser" title="Editar
                                         Usuario" style="background-color: #48b0f7;!important; color:white!important;">
                    <i class="fa fa-edit"></i>
                </button>
            </div>
        </td>
    </tr>
	<?php endforeach ?>
    </tbody>
</table>