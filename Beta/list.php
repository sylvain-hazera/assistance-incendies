<?php
include("header_aggrid.php");

include_once "./includes/classes/Category.php";
include_once "./includes/classes/City.php";
include_once "./includes/classes/Offer.php";
include_once "./includes/classes/Contact.php";

$categories = Category::loadAll();
$cities = City::loadAll();
$offers = Offer::loadAll();

$rows = [];
/** @var Offer $offer */
foreach ($offers as $offer) {
    $row = [
        'id' => $offer->getId(),
        'category' => $offer->getCategory()->getName(),
        'city' => $offer->getCity()->getName(),
        'description' => $offer->getDescription(), ENT_QUOTES
    ];
    $rows[] = $row;
}
$rowData = json_encode($rows);
$rowData = str_replace("'", "\\'", $rowData);
?>
<div class="container">
    <div class="row jumbotron jumbotron-fluid">
        <div class="container mt-4">
            <h1 class="display-6">Urgences - Mise en relation</h1>
            <p class="lead">Vous avez besoin d'aide ? Cherchez dans les annonces ci-dessous celles qui pourraient vous aider.</p>
            <hr class="my-4">
        </div>
    </div>
</div>

<div>
    <div class="container">
        <p class="text-muted">Cliquer sur un élément d'une ligne pour avoir le détail de l'annonce.</p>
    </div>

    <div id="myGrid"
         class="ag-theme-alpine">
    </div>
</div>
<style>
    a {
        color: inherit; /* blue colors for links too */
        text-decoration: inherit; /* no underline */
    }
</style>
<script type="application/javascript">
    <?php

    echo
    "const rowData = JSON.parse('{$rowData}');";
    ?>

    const preUrl = 'contact-announcer.php?offer=';

    const columnDefs = [
        {
            headerName: 'Type',
            field: 'category',
        },
        {
            headerName: 'Commune',
            field: 'city',
        },
        {
            headerName: 'Description',
            field: 'description',
        },
    ];

    const gridOptions = {
        columnDefs: columnDefs,
        rowData: rowData,
        defaultColDef:
            {
                sortable:true,
                floatingFilter: true,
                filter: true,
                suppressMenu: true
            },
        pagination:true,
        paginationPageSize: 50,
        onRowClicked: function (param) {
            window.location.href = 'contact-announcer.php?offer=' + param.data.id;
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        const gridDiv = document.querySelector('#myGrid');
        new agGrid.Grid(gridDiv, gridOptions);
        gridDiv.style.setProperty('width', '100%');
        gridDiv.style.setProperty('height', '600px');
        gridOptions.api.sizeColumnsToFit();
    });
</script>

<?php
include("footer.php");
?>

