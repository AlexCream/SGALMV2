<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'conexion.php';

// Procesar formulario de agregar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $producto = $conexion->real_escape_string($_POST['producto']);
    $cantidad = intval($_POST['cantidad']);
    $precio = floatval($_POST['precio']);
    
    $stmt = $conexion->prepare("INSERT INTO inventario (producto, cantidad, precio) VALUES (?, ?, ?)");
    $stmt->bind_param("sid", $producto, $cantidad, $precio);
    $stmt->execute();
}

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $conexion->prepare("DELETE FROM inventario WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Obtener registros
$registros = [];
$result = $conexion->query("SELECT * FROM inventario ORDER BY id DESC");
if ($result) {
    $registros = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Automotriz Los Amigos</title>
    <style>
        /* ESTILOS LOGIN */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }
        
        /* MENÚ DE NAVEGACIÓN */
        .main-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #333;
    padding: 10px 20px;
    color: white;
}

        
        .nav-left {
            display: flex;
            align-items: center;
            gap: 20px;
            justify-self: start;
        }
        
        .nav-logo {
            height: 84px;
        }
        
        .nav-links {
            display: flex;
            gap: 15px;
        }
        
        .nav-title {
            margin: 0;
            font-size: 24px;
            text-align: center;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .nav-right {
            justify-self: end;
        }
        
        .nav-links a, .nav-logout {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        
        .nav-links a:hover, .nav-logout:hover {
            text-decoration: underline;
        }
        
        /* CONTENIDO PRINCIPAL */
        .main-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .section-title {
            color: #444;
            font-size: 20px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        /* FORMULARIO DE AGREGAR */
        .add-form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 15px;
        }
        
        .btn {
            padding: 10px 20px;
            background-color: #555;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .btn:hover {
            background-color: #444;
        }
        
        .btn-primary {
            background-color: #28a745;
        }
        
        .btn-primary:hover {
            background-color: #218838;
        }
        
        .btn-danger {
            background-color: #dc3545;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        /* TABLA DE INVENTARIO */
        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .inventory-table th, 
        .inventory-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .inventory-table th {
            background-color: #333;
            color: white;
        }
        
        .inventory-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .inventory-table tr:hover {
            background-color: #f1f1f1;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        /* FOOTER */
        .footer {
            background-color: #333;
            color: #aaa;
            padding: 20px 0;
            text-align: center;
            font-size: 14px;
            margin-top: 40px;
            border-top: 1px solid #444;
        }
    </style>
</head>
<body>
    <!-- MENÚ DE NAVEGACIÓN CON NUEVA ESTRUCTURA -->
    <nav class="main-nav">
        <div class="nav-left">
            <img src="logo/LOGO.jpg" alt="Logo" class="nav-logo">
            <div class="nav-links">
                <a href="index.php">Inicio</a>
                <a href="inventario.php">Catálogo</a>
            </div>
        </div>
        
        <div class="nav-title" style="text-align: center; flex: 1;">
    <h1 style="margin: 0;">AUTOMOTRIZ LOS AMIGOS</h1>
</div>

        
        <div class="nav-right">
            <a href="logout.php" class="nav-logout">Cerrar sesión</a>
        </div>
    </nav>
    
    <!-- CONTENIDO PRINCIPAL -->
    <div class="main-container">
        <h2 class="section-title">Gestión de Inventario</h2>
        
        <!-- FORMULARIO PARA AGREGAR PRODUCTOS -->
        <div class="add-form">
            <h3>Agregar Nuevo Producto</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="producto">Nombre del Producto</label>
                    <input type="text" class="form-control" id="producto" name="producto" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="cantidad">Cantidad</label>
                        <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="precio">Precio Unitario</label>
                        <input type="number" class="form-control" id="precio" name="precio" step="0.01" min="0" required>
                    </div>
                </div>
                
                <button type="submit" name="agregar" class="btn btn-primary">Agregar Producto</button>
            </form>
        </div>
        
        <!-- TABLA DE INVENTARIO -->
        <h3>Listado de Productos</h3>
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th class="text-right">Cantidad</th>
                    <th class="text-right">Precio Unitario</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($registros) > 0): ?>
                    <?php foreach ($registros as $item): ?>
                    <tr>
                        <td><?= $item['id'] ?></td>
                        <td><?= htmlspecialchars($item['producto']) ?></td>
                        <td class="text-right"><?= $item['cantidad'] ?></td>
                        <td class="text-right">$<?= number_format($item['precio'], 2) ?></td>
                        <td class="text-center">
                            <a href="?eliminar=<?= $item['id'] ?>" class="btn btn-danger" 
                               onclick="return confirm('¿Estás seguro de eliminar este producto?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No hay productos en el inventario</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- FOOTER -->
    <footer class="footer">
        Sistema de Gestión Automotriz © <?php echo date('Y'); ?>
    </footer>
</body>
</html>