
  <link rel="stylesheet" href="<?php LoadStatic(); echo GetStaticFile('board','board.css')?>">
    <?php
                  $db = new model();
                  $table = $_SESSION['table'];
                  $db->prepare("SHOW COLUMNS FROM $table");
                  $fields = $db->GetAll();
                  $items = explode('/',$_GET['path']);
                  $id = end($items);
                  $key = $fields[0]['Field'];
                  foreach($fields as $item){
                      if($item['Key'] == "PRI"){
                          $key = $item['Field'];
                      }
                  }
                  $db->prepare("SELECT * FROM $table WHERE $key=:id");
                  $db->bind(":id",$id);
                  $result = $db->GetAll();
                  if(count($result) == 0){
                    header("location: /admin/dashboard");
                    die();
                  }
                  $row = $result[0];
                  
                  $i = 1;
          ?>
        <main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">
          <h1>Dashboard</h1>          
          <h2>Change <?php echo $table?> with id <?php echo $id;?></h2>
                
            <form method="POST" id="Changes">
              <?php foreach($fields as $keyname):
                $fieldname = $keyname['Field'];
                $type = strtolower( $keyname['Type']);
              ?>
              <br> 
              <label > <?php echo $fieldname; ?></label>
              <?php  if(preg_match('/int/',$type)):?>
                <input type="number" class='form-control' name="<?php echo $fieldname;?>" value="<?php echo $row[$fieldname]; ?>"> 
              <?php elseif(preg_match('/varchar/',$type)):?>
                <input type="text" class='form-control' name="<?php echo $fieldname;?>" value="<?php echo $row[$fieldname]; ?>"> 
              <?php elseif(preg_match('/text/',$type)):?>
                <textarea class='form-control' name="<?php echo $fieldname;?>" ><?php echo $row[$fieldname];?></textarea>
              <?php else:?>
                <input type="text" class='form-control' name="<?php echo $fieldname;?>" value="<?php echo $row[$fieldname]; ?>"> 
              <?php endif;  endforeach;?>
              <br>
              <input type="submit" class="btn btn-success" value="save">
            </form>
          </div>
        </main>
      </div>
    </div>
    <script>
    $("#Changes").submit(function(){
        var GetData = $('#Changes').serialize();
        console.log(GetData);
        $.ajax({
          method: "POST",
          url: "/admin/saveItem",
          data: GetData
        })
          .done(function( msg ) {
            if(msg=="Done"){
              alert("Changed");
            }else{
              alert("Failed to change table check the foreign keys");
            }
          });
      });
    </script>