<?php

require_once __DIR__ . '/config.php';

$errores = [];
$sabor = '';
$precio = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        http_response_code(403);

        exit(
            'Solicitud no válida. Recarga el formulario e inténtalo otra vez.'
        );
    }

    $saborRecibido = $_POST['sabor'] ?? '';
    $precioRecibido = $_POST['precio'] ?? '';

    $sabor = is_string($saborRecibido)
        ? trim($saborRecibido)
        : '';

    $precio = is_string($precioRecibido)
        ? trim($precioRecibido)
        : '';

    if ($sabor === '') {
        $errores[] = 'Indica el sabor de la torta.';
    } elseif (mb_strlen($sabor, 'UTF-8') > 255) {
        $errores[] = 'El sabor no debe superar los 255 caracteres.';
    }

    $precioEntero = null;

    if ($precio === '') {
        $errores[] = 'Indica el precio de la torta.';
    } else {
        $precioEntero = filter_var(
            $precio,
            FILTER_VALIDATE_INT
        );

        if ($precioEntero === false) {
            $errores[] = 'El precio va en digitos, sin centavos.';
        } elseif (
            $precioEntero < -2147483648 ||
            $precioEntero > 2147483647
        ) {
            $errores[] = 'El precio está fuera del rango permitido.';
        }
    }

    if (count($errores) === 0) {
        try {
            $sentencia = $conexion->prepare(
                'INSERT INTO tortas (sabor, precio) VALUES (?, ?)'
            );

            $sentencia->bind_param(
                'si',
                $sabor,
                $precioEntero
            );

            $sentencia->execute();
            $sentencia->close();

            header('Location: index.php', true, 303);
            exit;
        } catch (mysqli_sql_exception $error) {
            error_log($error->getMessage());

            $errores[] =
                'No se pudo guardar la torta. Inténtalo nuevamente.';
        }
    }
}

require __DIR__ . '/encabezado.php';
?>

<h2>Registrar una torta</h2>

<?php if (count($errores) > 0): ?>
    <div role="alert">
        <p>No se pudo registrar la torta:</p>

        <ul>
            <?php foreach ($errores as $error): ?>
                <li><?= escapar($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="nuevo.php" method="POST">
    <input
        type="hidden"
        name="csrf_token"
        value="<?= escapar($_SESSION['csrf_token']) ?>"
    >

    <div>
        <label for="sabor">Sabor de la torta:</label>

        <input
            type="text"
            id="sabor"
            name="sabor"
            maxlength="255"
            value="<?= escapar($sabor) ?>"
        >
    </div>

    <br>

    <div>
        <label for="precio">Precio en Bs:</label>

        <input
            type="number"
            id="precio"
            name="precio"
            step="1"
            value="<?= escapar($precio) ?>"
        >
    </div>

    <br>

    <button type="submit">Registrar torta</button>
</form>

<p>
    <a href="index.php">Volver a la lista</a>
</p>

<?php require __DIR__ . '/pie.php'; ?>