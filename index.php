<?php  
$insert = false;
$update = false;
$delete = false;

// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$database = "inote";
$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle deletion
if (isset($_GET['delete'])) {
    $sno = $_GET['delete'];
    $delete = true;
    $sql = "DELETE FROM `inote` WHERE `Sr.no` = '$sno'";
    $result = mysqli_query($conn, $sql);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['snoEdit'])) {
        // Update record
        $sno = $_POST["snoEdit"] ?? '';
        $title = $_POST["titleEdit"] ?? '';
        $description = $_POST["descriptionEdit"] ?? '';

        if ($sno && $title && $description) {
            $sql = "UPDATE `inote` SET `Title` = '$title', `Description` = '$description' WHERE `Sr.no` = '$sno'";
            $result = mysqli_query($conn, $sql);
            $update = $result ? true : false;
        }
    } else {
        // Insert new record
        $title = $_POST["title"] ?? '';
        $description = $_POST["description"] ?? '';

        if ($title && $description) {
            $sql = "INSERT INTO `inote` (`Title`, `Description`) VALUES ('$title', '$description')";
            $result = mysqli_query($conn, $sql);
            $insert = $result ? true : false;
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>iNotes - Notes taking made easy</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">iNotes</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="#">About</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Contact Us</a></li>
        </ul>
    </div>
</nav>

<?php
if ($insert) {
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
    <strong>Success!</strong> Your note has been inserted successfully.
    <button type='button' class='close' data-dismiss='alert'><span>&times;</span></button>
  </div>";
}
if ($delete) {
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
    <strong>Success!</strong> Your note has been deleted successfully.
    <button type='button' class='close' data-dismiss='alert'><span>&times;</span></button>
  </div>";
}
if ($update) {
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
    <strong>Success!</strong> Your note has been updated successfully.
    <button type='button' class='close' data-dismiss='alert'><span>&times;</span></button>
  </div>";
}
?>

<div class="container my-4">
    <h2>Add a Note to iNotes</h2>
    <form action="/inotes/index.php" method="POST">
        <div class="form-group">
            <label for="title">Note Title</label>
            <input type="text" class="form-control" name="title" id="title" required>
        </div>
        <div class="form-group">
            <label for="description">Note Description</label>
            <textarea class="form-control" name="description" id="description" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Note</button>
    </form>
</div>

<div class="container my-4">
    <table class="table" id="myTable">
        <thead>
            <tr>
                <th scope="col">Sr.No</th>
                <th scope="col">Title</th>
                <th scope="col">Description</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $sql = "SELECT * FROM `inote`";
                $result = mysqli_query($conn, $sql);
                $sno = 0; 
                while($row = mysqli_fetch_assoc($result)) {
                    $sno++;
                    echo "<tr>
                    <th scope='row'>". $sno . "</th>
                    <td>". $row['Title'] . "</td>
                    <td>". $row['Description'] . "</td>
                    <td>
                        <button class='btn btn-sm btn-primary edit' id=".$row['Sr.no']." data-toggle='modal' data-target='#editModal'>Edit</button> 
                        <button class='btn btn-sm btn-danger delete' id=d".$row['Sr.no'].">Delete</button>
                    </td>
                  </tr>";
                }
            ?>
        </tbody>
    </table>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit this Note</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form action="/inotes/index.php" method="POST">
                    <input type="hidden" name="snoEdit" id="snoEdit">
                    <div class="form-group">
                        <label for="titleEdit">Note Title</label>
                        <input type="text" class="form-control" name="titleEdit" id="titleEdit">
                    </div>
                    <div class="form-group">
                        <label for="descriptionEdit">Note Description</label>
                        <textarea class="form-control" name="descriptionEdit" id="descriptionEdit" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Note</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Dependencies -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function () {
        $('#myTable').DataTable();
    });

    // JavaScript for handling the edit modal
    let titleEdit = document.getElementById('titleEdit');
    let descriptionEdit = document.getElementById('descriptionEdit');
    let snoEdit = document.getElementById('snoEdit');

    document.querySelectorAll('.edit').forEach((element) => {
        element.addEventListener("click", (e) => {
            let tr = e.target.closest('tr');
            let title = tr.getElementsByTagName("td")[0].innerText;
            let description = tr.getElementsByTagName("td")[1].innerText;
            titleEdit.value = title;
            descriptionEdit.value = description;
            snoEdit.value = e.target.id;
        });
    });

    // JavaScript for handling delete
    document.querySelectorAll('.delete').forEach((element) => {
        element.addEventListener("click", (e) => {
            let sno = e.target.id.substr(1);
            if (confirm("Are you sure you want to delete this note?")) {
                window.location = `/inotes/index.php?delete=${sno}`;
            }
        });
    });
</script>

</body>
</html>
