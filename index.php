<?php
if (isset($_POST["submit"])) {

  $target_dir = "uploads/";
  $target_file = $target_dir . "uploaded-" . basename($_FILES["file_upload"]["name"]);

  if (move_uploaded_file($_FILES["file_upload"]["tmp_name"], $target_file)) {
    echo "File uploaded successfully.";
  } else {
    echo "Sorry, there was an error uploading your file.";
  }
}
?>
<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="utf-8" />

  <title>Assessment</title>
  <script type="text/javascript" src="script.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
</head>

<body>
  <div class="container p-3">
    <form action="upload.php" method="post" enctype="multipart/form-data">
      <div class="row">
        <h3>Export Booking Excel to Coprar Converter</h3>
      </div>

      <div class="row">
        <label class="col-lg-12">Receiver Code: </label>
        <input class="col-lg-12 mx-2" type="text" value="RECEIVER" />
        <p class="col-lg-12"><small>Please change before file select.</small></p>
      </div>

      <div class="row">
        <label class="col-lg-12">Callsign Code:</label>
        <input class="col-lg-12 mx-2" type="text" value="XXXXX" />
        <p class="col-lg-12"><small>Please change before file select.</small></p>
      </div>

      <div class="row">
        <label class="col-lg-12">Export booking excel file: </label>
        <input class="col-lg-12" type="file" name="file_upload" id="file_upload" />
        <input class="mx-2 mt-2" type="submit" value="Upload" name="submit" />
        <p class="col-lg-12"><small><a href="#" target="_new">Sample Excel</a></small></p>

      </div>

      <div class="row">
        <textarea rows="30" class="col mx-2" id="file_display">
          <?php
          $filename = "uploads/uploaded-sample.xlsx";
          if (file_exists($filename)) {
            require 'vendor/autoload.php';
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filename);
            $worksheet = $spreadsheet->getActiveSheet();
            $edi = "";
            foreach ($worksheet->getRowIterator() as $row) {
              $cellIterator = $row->getCellIterator();
              $cellIterator->setIterateOnlyExistingCells(false);
              foreach ($cellIterator as $cell) {
                $edi .= $cell->getValue() . "+";
              }
            }
            echo $edi;
          }
          ?>
        </textarea>
      </div>
    </form>
  </div>
</body>

</html>