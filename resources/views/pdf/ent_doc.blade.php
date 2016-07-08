<!DOCTYPE html>
<html>
<head>
    <style>
        table {
            border-collapse: collapse;
        }

        table, td, th {
            border: 1px solid black;
        }
    </style>
</head>
<body>
<div class="container">
    <?php  if(isset($persons) && !empty($persons)):  ?>
    <table class="table table-info" style="border: 1px solid black; border-collapse: collapse;">
        <thead>
        <tr class="table table-info">
            <?php foreach ($EentityTitles as $EentityTitle) : ?>
            <?php if ($EentityTitle == 'applicants') {
                echo "<th>Aplicant</th>";
            } elseif ($EentityTitle == 'name') {
                echo "<th>Entity name</th>";
            } elseif ($EentityTitle == 'existing_type') {
                echo "<th>Existing Type</th>";
            } elseif ($EentityTitle == 'currency.name') {
                echo "<th>Currency</th>";
            } elseif ($EentityTitle == 'day') {
                echo "<th>Credit day</th>";
            } elseif ($EentityTitle == 'default_address') {
                echo "<th>Address</th>";
            } elseif ($EentityTitle == 'notes') {
                echo "<th>Notes</th>";
            }
                ?>
                <?php endforeach; ?>
        </tr>
        </thead>
        <?php foreach ($persons as $person): ?>
        <tr class="table table-info">
            <?php foreach ($person as $value): ?>
            <td>
                <?php echo $value ?>
            </td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>

</body>
</html>