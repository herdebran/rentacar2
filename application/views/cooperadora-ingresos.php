<form class="form-horizontal" action="#" method="POST" accept-charset="utf-8">
    <fieldset>
        <h3>Historial de Ingresos</h3>
        <p>Seleccione un periodo de busqueda</p>

        <div class="form-group col-xs-12 col-sm-4 col-md-4 col-lg-4">
            <label for="fdesde" class="col-xs-4 control-label">Desde</label>
            <div class="col-xs-8">
                <input type="text" class="form-control datepicker" id="fdesde" name="fdesde"  value="<?php echo date('d/m/Y', strtotime("-1 month", time()))?>">
            </div>
        </div>

        <div class="form-group col-xs-12 col-sm-4 col-md-4 col-lg-4">
            <label for="fhasta" class="col-xs-4 control-label">Hasta</label>
            <div class="col-xs-8">
                <input type="text" class="form-control datepicker" id="fhasta" name="fhasta" value="<?php echo date('d/m/Y') ?>">
            </div>
        </div>

        <div class="form-group col-xs-12 col-sm-4 col-md-4 col-lg-4">
            <div class="col-xs-12">
                <button id="btnBuscar" type="button" class="btn btn-primary">Buscar</button>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12">
                <a href="/gestion-cooperadora" id="btnBuscar" type="button" class="btn btn-primary">Volver</a>
            </div>
        </div>
    </fieldset>
</form>

<fieldset>
    <h3>Resultados</h3>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped table-hover table-condensed display" id="table-resultados" style="width: 100% !important">
                <thead>
                    <tr>
                        <th>Operacion</th>
                        <th>Fecha</th>
                        <th>Apellido y Nombre</th>
                        <th>Documento</th>
                        <th>Cuotas</th>
                        <th>Cobrador</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</fieldset>

<script>
    $.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: '< Ant',
        nextText: 'Sig >',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 7,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
    $(".datepicker").datepicker();
    $().ready(function () {
        // DATATABLE------------------------------------------------------------------------//
        // Variable global donde se aloja el objeto datatable.
        var table;
        table = $('#table-resultados').removeAttr('width').DataTable({
            language: {
                url: "http://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
            },
            dom: 'Bfrtip',
            buttons: [
                'excel',
                'pdf'
            ],
            scrollX: true,
            ajax: {
                url: "/cooperadora/ajaxBuscarOperaciones",
                method: "post",
                data: function (data) {
                    data = {};
                    data.fechadesde =$("#fdesde").val();
                    data.fechahasta = $("#fhasta").val();
                    return {'filtros': data};
                },
                success(response) {
                    if (response["error"] == "0") {
                        table.clear().rows.add(response["data"]).draw()
                    } else {
                        table.clear().rows.add("").draw()
                        alert(response["data"]);
                    }
                },
                error: function (xhr, error, thrown) {
                    alert("Ha ocurrido un error, contactese con el administrador.");
                },
                complete: function(){
                    $("#btnBuscar").attr("disabled", false);
                }
            },
            columns: [
                {data: 'idoperacion'},
                {data: 'fecha'},
                {data: 'nombreyapellido',
                    render: function (data, type, row, meta) {
                        return row["nombre"] + " " + row["apellido"];
                    }
                },
                {data: 'documento'},
                {data: 'cuotas'},
                {data: 'cobrador'},
                {data: 'montototal'}
            ]
        });
        // Form functions ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        $("#btnBuscar").click(function () {
            $(this).attr("disabled", true);
            table.ajax.reload();
        });
    });
</script>
