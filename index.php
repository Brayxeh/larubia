<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
include 'conexion.php';

$sql = "SELECT * FROM clientes";
$result = $conn->query($sql);

$sql_articulos = "SELECT * FROM articulos";
$result_articulos = $conn->query($sql_articulos);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La rubia - cafeteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
            padding-top: 60px; 
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .articulo-row {
            align-items: flex;
            display: flex-end;
        }
        #facturaGuardadaModal .modal-footer {
            display: flex;
            justify-content: space-between;
        }
        #facturaGuardadaModal .modal-footer .btn {
            flex: 0 0 48%;
        }
        .articulo-row .form-control {
        height: calc(2.25rem + 2px); 
        }
        .articulo-row .btn {
        height: calc(2.25rem + 2px); 
        display: flex;
        align-items: center;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Cafeteria - La rubia</h1>
        <form id="facturaForm" action="registrar_factura.php" method="POST">
            <div class="mb-3">
                <label for="cliente_matricula" class="form-label">Matrícula del cliente</label>
                <input type="text" class="form-control" id="cliente_matricula" name="cliente_matricula" maxlength="8" required>
                <button type="button" class="btn btn-secondary mt-2" id="checkCliente">Verificar cliente</button>
            </div>
            <div id="clienteInfo" style="display: none;">
                <div class="mb-3">
                    <label for="cliente_id" class="form-label">Cliente</label>
                    <select class="form-control" id="cliente_id" name="cliente_id">
                        <?php while($row = $result->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="articulos" class="form-label">Artículos</label>
                    <div id="articulos">
                        <div class="row mb-2 articulo-row">
                            <div class="col-md-5">
                                <label for="articulo" class="form-label">Artículo</label>
                                <select class="form-control articulo-select" name="articulos[0][id]" required>
                                    <option value="">Seleccione un artículo</option>
                                    <?php 
                                    $result_articulos->data_seek(0); 
                                    while($row = $result_articulos->fetch_assoc()): ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?> - $<?php echo $row['precio']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="cantidad" class="form-label">Cantidad</label>
                                <input type="number" class="form-control cantidad" name="articulos[0][cantidad]" min="1" value="1" required>
                            </div>
                            <div class="col-md-2">
                                <label for="precio" class="form-label">Precio</label>
                                <input type="text" class="form-control precio" name="articulos[0][precio]" readonly>
                            </div>
                            <div class="col-md-2">
                                <label for="total" class="form-label">Total</label>
                                <input type="text" class="form-control total" name="articulos[0][total]" readonly>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-articulo">&times;</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success" id="addArticulo">Agregar artículo</button>
                </div>
                <div class="mb-3">
                    <label for="total" class="form-label">Precio total con impuestos incluidos</label>
                    <input type="number" class="form-control" id="total" name="total" readonly>
                </div>
                <div class="mb-3">
                    <label for="comentario" class="form-label">Comentario</label>
                    <textarea class="form-control" id="comentario" name="comentario" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" id="guardarFacturaBtn">Guardar Factura</button>
            </div>
        </form>
    </div>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Registrar Nuevo Cliente</h2>
            <form id="registrarClienteForm">
                <div class="mb-3">
                    <label for="modal_matricula" class="form-label">Matrícula</label>
                    <input type="text" class="form-control" id="modal_matricula" name="modal_matricula" maxlength="8" required>
                </div>
                <div class="mb-3">
                    <label for="modal_nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="modal_nombre" name="modal_nombre" required>
                </div>
                <button type="submit" class="btn btn-primary">Registrar Cliente</button>
            </form>
        </div>
    </div>


    <div id="facturaGuardadaModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="facturaDetalles">
             
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="imprimirFacturaBtn">Imprimir</button>
                <button class="btn btn-secondary" id="seguirFacturandoBtn">Seguir Facturando</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            var articuloIndex = 1;

            $('#checkCliente').click(function() {
                var matricula = $('#cliente_matricula').val();
                if (matricula.length == 8) {
                    $.ajax({
                        url: 'verificar_cliente.php',
                        type: 'POST',
                        data: { matricula: matricula },
                        success: function(response) {
                            var data = JSON.parse(response);
                            if (data.existe) {
                                $('#cliente_id').val(data.cliente_id);
                                $('#clienteInfo').show();
                            } else {
                                $('#modal_matricula').val(matricula);
                                $('#myModal').show();
                            }
                        }
                    });
                } else {
                    alert('La matrícula debe tener 8 caracteres.');
                }
            });

            $('#addArticulo').click(function() {
                if (articuloIndex < 5) {
                    var newArticuloRow = `
                       <div class="row mb-2 articulo-row">
                            <div class="col-md-5">
                                <label for="articulo" class="form-label">Artículo</label>
                                    <select class="form-control articulo-select" name="articulos[0][id]" required>
                                    <option value="">Seleccione un artículo</option>
                                <?php 
                    $result_articulos->data_seek(0); 
                    while($row = $result_articulos->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?> - $<?php echo $row['precio']; ?></option>
                    <?php endwhile; ?>
                    </select>
                    </div>
                        <div class="col-md-2">
                         <label for="cantidad" class="form-label">Cantidad</label>
                            <input type="number" class="form-control cantidad" name="articulos[0][cantidad]" min="1" value="1" required>
                            </div>
                        <div class="col-md-2">
                        <label for="precio" class="form-label">Precio</label>
                     <input type="text" class="form-control precio" name="articulos[0][precio]" readonly>
                        </div>
                    <div class="col-md-2">
                    <label for="total" class="form-label">Total</label>
                    <input type="text" class="form-control total" name="articulos[0][total]" readonly>
                    </div>
                    <div class="col-md-1 d-flex justify-content-center align-items-end">
                    <button type="button" class="btn btn-danger remove-articulo">&times;</button>
                    </div>
                        </div>
                    `;
                    $('#articulos').append(newArticuloRow);
                    articuloIndex++;
                } else {
                    alert('No se pueden agregar más de 5 artículos.');
                }
            });

            $(document).on('click', '.remove-articulo', function() {
                $(this).closest('.articulo-row').remove();
                articuloIndex--;
                actualizarTotal();
            });

            $(document).on('change', '.articulo-select', function() {
                var articulo_id = $(this).val();
                var row = $(this).closest('.row');
                $.ajax({
                    url: 'obtener_precio_articulo.php',
                    type: 'POST',
                    data: { articulo_id: articulo_id },
                    success: function(response) {
                        var data = JSON.parse(response);
                        row.find('.precio').val(data.precio);
                        var cantidad = row.find('.cantidad').val();
                        var total = data.precio * cantidad;
                        row.find('.total').val(total.toFixed(2));
                        actualizarTotal();
                    }
                });
            });

            $(document).on('input', '.cantidad', function() {
                var row = $(this).closest('.row');
                var precio = parseFloat(row.find('.precio').val());
                var cantidad = parseInt($(this).val());
                var total = precio * cantidad;
                row.find('.total').val(total.toFixed(2));
                actualizarTotal();
            });

            function actualizarTotal() {
                var total = 0;
                $('.total').each(function() {
                    total += parseFloat($(this).val());
                });
                var totalConImpuestos = total + (total * 0.18);
                $('#total').val(totalConImpuestos.toFixed(2));
            }

            var modal = document.getElementById("myModal");
            var facturaGuardadaModal = document.getElementById("facturaGuardadaModal");
            var span = document.getElementsByClassName("close");

            for (var i = 0; i < span.length; i++) {
                span[i].onclick = function() {
                    this.parentElement.parentElement.style.display = "none";
                }
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
                if (event.target == facturaGuardadaModal) {
                    facturaGuardadaModal.style.display = "none";
                }
            }

            $('#registrarClienteForm').on('submit', function(event) {
                event.preventDefault();
                var matricula = $('#modal_matricula').val();
                var nombre = $('#modal_nombre').val();
                $.ajax({
                    url: 'guardar_cliente.php',
                    type: 'POST',
                    data: { matricula: matricula, nombre: nombre },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.success) {
                            $('#cliente_id').append(new Option(nombre, data.cliente_id));
                            $('#cliente_id').val(data.cliente_id);
                            $('#clienteInfo').show();
                            modal.style.display = "none";
                        } else {
                            alert('Error al registrar el cliente: ' + data.error);
                        }
                    }
                });
            });

            $('#facturaForm').on('submit', function(event) {
                event.preventDefault();
                $.ajax({
                    url: 'registrar_factura.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.success) {
                            $('#guardarFacturaBtn').text('Guardar factura');
                            $('#facturaDetalles').html(generarDetallesFactura());
                            $('#facturaGuardadaModal').show();
                        } else {
                            alert('Error al registrar la factura: ' + data.error);
                        }
                    }
                });
            });

            $('#imprimirFacturaBtn').click(function() {
                window.print();
            });

            $('#seguirFacturandoBtn').click(function() {
                $('#facturaForm')[0].reset();
                $('#clienteInfo').hide();
                $('#facturaGuardadaModal').hide();
                articuloIndex = 1;
                $('#articulos').html(`
                    <div class="row mb-2 articulo-row">
                        <div class="col-md-5">
                            <label for="articulo" class="form-label">Artículo</label>
                            <select class="form-control articulo-select" name="articulos[0][id]" required>
                                <option value="">Seleccione un artículo</option>
                                <?php 
                                $result_articulos->data_seek(0); 
                                while($row = $result_articulos->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?> - $<?php echo $row['precio']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="cantidad" class="form-label">Cantidad</label>
                            <input type="number" class="form-control cantidad" name="articulos[0][cantidad]" min="1" value="1" required>
                        </div>
                        <div class="col-md-2">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="text" class="form-control precio" name="articulos[0][precio]" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="total" class="form-label">Total</label>
                            <input type="text" class="form-control total" name="articulos[0][total]" readonly>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-articulo">&times;</button>
                        </div>
                    </div>
                `);
            });

            $('#cliente_id').change(function() {
                $('#facturaForm')[0].reset();
                $('#clienteInfo').hide();
                articuloIndex = 1;
                $('#articulos').html(`
                    <div class="row mb-2 articulo-row">
                        <div class="col-md-5">
                            <label for="articulo" class="form-label">Artículo</label>
                            <select class="form-control articulo-select" name="articulos[0][id]" required>
                                <option value="">Seleccione un artículo</option>
                                <?php 
                                $result_articulos->data_seek(0); 
                                while($row = $result_articulos->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?> - $<?php echo $row['precio']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="cantidad" class="form-label">Cantidad</label>
                            <input type="number" class="form-control cantidad" name="articulos[0][cantidad]" min="1" value="1" required>
                        </div>
                        <div class="col-md-2">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="text" class="form-control precio" name="articulos[0][precio]" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="total" class="form-label">Total</label>
                            <input type="text" class="form-control total" name="articulos[0][total]" readonly>
                        </div>
                        <div class="col-md-1 d-flex align-items-center">
                            <button type="button" class="btn btn-danger remove-articulo">&times;</button>
                        </div>
                    </div>
                `);
            });

            function generarDetallesFactura() {
                var detalles = `
                    <h3>Detalles de la Factura</h3>
                    <p><strong>Cliente:</strong> ${$('#cliente_id option:selected').text()}</p>
                    <p><strong>Comentario:</strong> ${$('#comentario').val()}</p>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Artículo</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                $('#articulos .articulo-row').each(function() {
                    var articulo = $(this).find('.articulo-select option:selected').text();
                    var cantidad = $(this).find('.cantidad').val();
                    var precio = $(this).find('.precio').val();
                    var total = $(this).find('.total').val();
                    detalles += `
                        <tr>
                            <td>${articulo}</td>
                            <td>${cantidad}</td>
                            <td>${precio}</td>
                            <td>${total}</td>
                        </tr>
                    `;
                });
                detalles += `
                        </tbody>
                    </table>
                    <p><strong>Precio Total con Impuestos Incluidos:</strong> ${$('#total').val()}</p>
                `;
                return detalles;
            }
        });
    </script>
</body>
</html>
