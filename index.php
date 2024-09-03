<?php
// Conexión a la base de datos usando PDO
$host = 'localhost'; // Cambia si es necesario
$db = 'gastronomia'; // Cambia por el nombre de tu base de datos
$user = 'root'; // Cambia si es necesario
$pass = ''; // Cambia si es necesario
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Manejo de acciones: crear, actualizar, eliminar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        $nombre = $_POST['nombre'];
        $stmt = $pdo->prepare('INSERT INTO categorias (nombre) VALUES (?)');
        $stmt->execute([$nombre]);
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $stmt = $pdo->prepare('UPDATE categorias SET nombre = ? WHERE id = ?');
        $stmt->execute([$nombre, $id]);
    }
} elseif (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM categorias WHERE id = ?');
    $stmt->execute([$id]);
}

// Obtener todas las categorías
$stmt = $pdo->query('SELECT * FROM categorias');
$categorias = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Categorías con PDO</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <h2>Listado de Categorías</h2>
    <form method="POST">
        <input type="hidden" name="id" id="categoria_id">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="categoria_nombre" required>
        <button type="submit" name="create">Añadir</button>
        <button type="submit" name="update">Actualizar</button>
    </form>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($categorias as $categoria): ?>
        <tr>
            <td><?= htmlspecialchars($categoria['id']) ?></td>
            <td><?= htmlspecialchars($categoria['nombre']) ?></td>
            <td>
                <button onclick="editarCategoria(<?= $categoria['id'] ?>, '<?= addslashes($categoria['nombre']) ?>')">Editar</button>
                <a href="?delete=<?= $categoria['id'] ?>" onclick="return confirm('¿Estás seguro de eliminar esta categoría?');">Eliminar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <script>
        function editarCategoria(id, nombre) {
            document.getElementById('categoria_id').value = id;
            document.getElementById('categoria_nombre').value = nombre;
        }
    </script>
</body>
</html>
