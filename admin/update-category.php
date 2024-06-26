<?php
include('../database/db_conn.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM category_table WHERE id=$id";
    $res = mysqli_query($conn, $sql);

    $count = mysqli_num_rows($res);

    if ($count == 1) {
        $row = mysqli_fetch_assoc($res);

        $title = $row['title'];
        $image = $row['image_name'];
        $in_stock = $row['in_stock'];
        $out_stock = $row['out_stock'];
    } else {
        $_SESSION['not-found'] = "<div style='background-color:red; position:absolute;top:200px;color:whitesmoke;padding:1rem;font-size:1.3rem;border-radius:10px;' class='msg'>Failed. Image Not Found..!!</div>";

        header("location:" . URL . 'admin/manage-category.php');
    }
} else {
    header("location:" . URL . 'admin/manage-category.php');
}

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width= , initial-scale=1.0">
    <title>Category</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;400;500;600;700;800;900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;

        }

        body {

            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color:whitesmoke;

        }

        form {
            display: flex;
            flex-direction: column;
            width: 600px;
            border: 1px solid black;
            padding: 1rem;
            margin-top: 4rem;
            margin-left: 4rem;
            border-radius: 10px;
    background-color: lightgray;

            height: 700px;
        }

        .title {
            font-size: 1.6rem;
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        h1 {
            margin-bottom: 2rem;
            color: red;
        }

        #btn {
            background-color: #E32636;
            padding: 0.8rem;
            font-size: 1.4rem;
            color: whitesmoke;
            border: none;
            border-radius: 10px;
            margin-top: 2rem;
            font-weight:600;
        }

        #in {
            padding: 1rem;
            font-size: 1rem;
        }

        #item{
          height: 100px;
          width: 100px;
        }
    </style>
</head>

<body>
    <div class="add-page">
        <form action="" method="post" enctype="multipart/form-data">
            <h1 style="text-align: center; color:black">Update Category</h1>
            <div class="title">
                <label for="title">Title</label>
                <input id="in" type="text" name="title" placeholder="Title" value="<?php echo $title; ?>">
            </div>
            <div class="title">
                <label for="title">Category Image</label>
                <?php
                if ($image != "") {
                ?>
                    <img src="<?php echo URL; ?>images/category/<?php echo $image; ?>" id="item">
                <?php
                } else {
                }
                ?>
            </div>
            <div class="title">
                <label for="title">New Image</label>
                <input id="in" type="file" name="image">
            </div>
            <div class="title">
                <label for="title">Available Now</label>
                <input type="radio" name="in_stock" value="yes" <?php if ($in_stock == "yes") {
                                                                    echo "checked";
                                                                } ?>>yes
                <input type="radio" name="in_stock" value="no" <?php if ($in_stock == "no") {
                                                                    echo "checked";
                                                                } ?>>no
            </div>
            <div class="title">
                <label for="title">Special Dish</label>
                <input type="radio" name="out_stock" value="yes" <?php if ($out_stock == "yes") {
                                                                        echo "checked";
                                                                    } ?>>yes
                <input type="radio" name="out_stock" value="no" <?php if ($out_stock == "no") {
                                                                    echo "checked";
                                                                } ?>>no
            </div>
            <input type="hidden" name="old_image" value="<?php echo $image; ?>">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input id="btn" type="submit" name="submit" value="Update">
        </form>

        <?php
        if (isset($_POST['submit'])) {
            $id = $_POST['id'];
            $title = $_POST['title'];
            $in_stock = $_POST['in_stock'];
            $out_stock = $_POST['out_stock'];

            $new_image_name = $_FILES['image']['name'];

            if ($new_image_name != "") {
                $extension = pathinfo($new_image_name, PATHINFO_EXTENSION);
                $new_image_name = "Category" . rand(000, 999) . '.' . $extension;
                $source_path = $_FILES['image']['tmp_name'];
                $destination_path = "../images/category/" . $new_image_name;

                // Handle file upload
                $upload = move_uploaded_file($source_path, $destination_path);

                if (!$upload) {
                    $_SESSION['upload'] = "<div style='background-color:red; position:absolute;top:240px;color:whitesmoke;padding:1rem;font-size:1.3rem;border-radius:10px;left:780px;' class='msg'> Category Updated Successfully.</div>";
                    header('location:' . URL . 'admin/manage-category.php');
                    die();
                }

                if ($image != "") {
                    $path = "../images/category/" . $image;
                    $delete = unlink($path);

                    if (!$delete) {
                        $_SESSION['failed'] = "<div style='background-color:red; position:absolute;top:200px;color:whitesmoke;padding:1rem;font-size:1.3rem;border-radius:10px;' class='msg'>Failed. Image Not Found..!!</div>";
                        header('location:' . URL . 'admin/manage-category.php');
                        die();
                    }
                }
            } else {
                $new_image_name = $image;
            }

            // Update image path in the database
            $sql_image = "UPDATE category_table SET image_name='$new_image_name' WHERE id='$id'";
            $res_image = mysqli_query($conn, $sql_image);

            if (!$res_image) {
                $_SESSION['update'] = 'Failed to update image path in the database';
                header("location:" . URL . 'admin/manage-category.php');
                exit();
            }

            // Update other fields in the database
            $sql2 = "UPDATE category_table SET title='$title', in_stock='$in_stock', out_stock='$out_stock' WHERE id='$id'";
            $res2 = mysqli_query($conn, $sql2);

            if ($res2 !== false && mysqli_affected_rows($conn) > 0) {
                echo "<div style='background-color:green; position:absolute;top:80px; left:810px; color:whitesmoke;padding:1rem;font-size:1.3rem;border-radius:10px;' class='msg'>Category Updated successfully</div>";
               
            } else {
                echo "<div style='background-color:green; position:absolute;top:80px;left:810px; color:whitesmoke;padding:1rem;font-size:1.3rem;border-radius:10px;' class='msg'>Category Updated successfully</div>";
                
            }
        }
        ?>
    </div>
</body>