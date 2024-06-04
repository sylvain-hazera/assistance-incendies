<?php
include("header.php");

?>
<style>
    .suggestions>li{
        list-style: none;
        padding:3px;
        background-color: #d6d6d6;
        margin-top: 1px;
    }
    .suggestions>li:hover{
        background-color: #a1c7dc;
    }

</style>
<div class="container">
    <div class="row jumbotron jumbotron-fluid">
        <div class="container mt-4">
            <h1 class="display-6">Service de centralisation des propositions et demandes en situation d'urgence</h1>
                    <p class="lead">Un évenement à créer ? C'est par ici.</p>
            </div>
            <hr class="my-1">
        </div>
    </div>
</div>

<div>
    <div class="container">
        <form>
<form method="post" action="create_event.php">
  <label for="name">Event Name:</label>
  <input type="text" id="name" name="name"><br>
  <label for="start_date">Start Date:</label>
  <input type="date" id="start_date" name="start_date"><br>
  <label for="status">Status:</label>
  <select id="status" name="status">
    <option value="open">Open</option>
    <option value="closed">Closed</option>
  </select><br>
  <input type="submit" value="Create Event">
</form>

    </form>
</div>
<?php
include("footer.php");
?>

