<?php

require_once __DIR__ . '/config.php';

try {
    $resultado = $conexion->query(
        'SELECT id, sabor, precio FROM tortas ORDER BY id ASC'
    );

    $tortas = $resultado->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $error) {
    error_log($error->getMessage());

    http_response_code(500);

    exit('No se pudo consultar la lista de tortas.');
}

require __DIR__ . '/encabezado.php';
?>

<p>
    En Pasteleria La Espiga preparamos tortas para compartir
    en familia y celebrar tus momentos especiales.
</p>

<h2>Nuestra vitrina</h2>

<p>
    Hay <?= count($tortas) ?> tortas en la vitrina.
</p>

<?php if (count($tortas) === 0): ?>
    <p>Todavia no hay tortas registradas.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Sabor de la torta</th>
                <th>Precio en Bs</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($tortas as $torta): ?>
                <tr>
                    <td>
                        <?= escapar($torta['sabor']) ?>
                    </td>

                    <td>
                        <?= escapar($torta['precio']) ?> Bs
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p>
    Vitrina atendida por <?= escapar(NOMBRE_COMPLETO) ?>
</p>

<p>
    <a href="nuevo.php">Registrar una nueva torta</a>
</p>

<?php require __DIR__ . '/pie.php'; ?>