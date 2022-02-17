<?php include "functions.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Small Business - Start Bootstrap Template</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
<!-- Responsive navbar-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container px-5">
        <a class="navbar-brand" href="#!">Photo Search</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="edit.php">Photo Editing</a></li>
                <li class="nav-item"><a class="nav-link" href="#!">Contact</a></li>
                <li class="nav-item"><a class="nav-link" href="#!">Services</a></li>
            </ul>
        </div>
    </div>
</nav>
<!-- Page Content-->
<div class="container px-4 px-lg-5">
    <!-- Heading Row-->
    <?php
    imageHandler();
    ?>
    <!--<div class="col-lg-7">
        <br><br>


    </div> -->
    <div class="col-lg-5">
        <div class="mb-3">

            <form action="" method="post" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="post_image">Add Image</label>
                    <input class="form-control" type="file" name="image">
                </div>
                <div class="form-group">
                    <label for="width">Width</label>
                    <input type="number" in="1" max="2000" class="form-control" name="width" placeholder="300">
                    <small id="emailHelp" class="form-text text-muted">Enter the width you want your photo to be.</small>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Height</label>
                    <input type="number" in="1" max="2000" class="form-control" name="height" placeholder="300">
                    <small id="emailHelp" class="form-text text-muted">Enter the height you want your photo to be.</small>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Image quality</label>
                    <input type="number" min="1" max="100" class="form-control" name="imageQuality" placeholder="100">
                    <small id="emailHelp" class="form-text text-muted">Quality (between 1 and 100)</small>
                </div>
                <div class="form-group" >
                    <label for="exampleInputEmail1">Watermark</label>
                    <input type="text" class="form-control" name="watermark" placeholder="Copyrighted">
                    <small id="emailHelp" class="form-text text-muted">Add a watermark to the image(optional)</small>
                </div>
                <p>
                    <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                        More watermark options
                    </button>
                </p>
                <div class="collapse" id="collapseExample">
                    <div class="card-body">
                    <div class="form-group" >
                        <label for="favcolor">Select watermark color:</label>
                        <input type="color" id="color" name="color" value="#ff0000">
                        <small id="emailHelp" class="form-text text-muted">Select watermark color(default is red)</small>
                    </div>
                    <div class="form-group" >
                        <label for="exampleInputEmail1">Position</label>
                        <input type="text" class="form-control" name="position" placeholder="Watermark Position">
                        <small id="emailHelp" class="form-text text-muted">Chose the position of your watermark(default is top left)</small>
                    </div>
                    </div>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="aspect">
                    <label class="form-check-label" for="aspect">Keep aspect ratio</label>
                    <small id="aspectHelp" class="form-text text-muted">If checked please only fill out one of the above fields.</small>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        </div>
    </div>
