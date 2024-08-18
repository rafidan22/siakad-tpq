<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Arsip Nilai</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Data Arsip Nilai</h1>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID Arsip</th>
                    <th>Jenis</th>
                    <th>Nilai</th>
                    <th>ID Materi</th>
                    <th>ID Data Santri</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($arsip_nilai as $nilai) : ?>
                <tr>
                    <td><?php echo $nilai['id_arsip']; ?></td>
                    <td><?php echo $nilai['jenis']; ?></td>
                    <td><?php echo $nilai['nilai']; ?></td>
                    <td><?php echo $nilai['id_materi']; ?></td>
                    <td><?php echo $nilai['id_datasantri']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
