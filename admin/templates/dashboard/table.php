
  <link rel="stylesheet" href="<?php LoadStatic(); echo GetStaticFile('board','board.css')?>">

        <main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">
          <h1>Table system</h1>  
          <h2>Tables</h2>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>name</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $db = new model();
                  $columnName = 'Tables_in_'.DB_Database;
                  $db->prepare("SHOW TABLES WHERE $columnName <> 'Users'");
                  $result = $db->GetAll();
                  $i = 1;
                ?>
                <?php  foreach($result as $items):
                      $item = $items[$columnName];
                   ?>
                <tr onclick="window.location = '/admin/table/<?php echo $item?>'">
                  <td><?php echo $i; $i++; ?></td>
                  <td><?php echo $item;?></td>
                </tr>
                <?php endforeach;?>
              </tbody>
            </table>
            </div>
        </main>
      </div>
    </div>
    <script>
    $("#Role").change(function(){
      var id = this.value;
      if(id != ""){
        $.ajax({
          method: "POST",
          url: "/admin/getPerm",
          data: {"id":id},
          dataType:"JSON"
        })
          .done(function( msg ) {
              $("#boxes input:checkbox").prop('checked',false);
              for(var i =0; i < msg.length; i++){
                var item = msg[i];
                $("#boxes #perm" + item['idPerm']).prop('checked',true);
              }
        });
      }

    });
    $("#boxes input:checkbox").click(function(){
      var idPerm = this.value;
      var check = $(this).is(':checked');
      var id = $("#Role").val();
      if(id != ""){
        if(check){
          check = 1;
        }else{
          check = 0;
        }
        var postdata = {"idRole":id,"idPerm":idPerm,"toggle":check};
        $.ajax({
            method: "POST",
            url: "/admin/setPerm",
            data: postdata
          })
            .done(function( msg ) {
                console.log(msg);
          });
      }
    });
    $("#ChangeRole").submit(function(){
      var Roleid = $("#RoleChange").val();
      var Userid = $("#UserChange").val();
      var postdata = {"idRole":Roleid,"idUser":Userid};
      $.ajax({
         method: "POST",
         url: "/admin/changeRole",
         data: postdata
      })
      .done(function( msg ) {
         console.log(msg);
      });
    });
     $("#createRole").submit(function(){
      var name = $("#NameRole").val();
      if(name == ""){
        return false;
      }
      var postdata = {"name":name};
      $.ajax({
         method: "POST",
         url: "/admin/createRole",
         data: postdata
      })
      .done(function( msg ) {
         if(msg == "Done"){
           alert("Added");
         }else{
           alert("Failed to add role");
         }
      });
      });

      $("#createPerm").submit(function(){
      var name = $("#NamePerm").val();
      console.log(name);
      if(name == ""){
        return false;
      }
      var postdata = {"name":name};
      $.ajax({
         method: "POST",
         url: "/admin/createPerm",
         data: postdata
      })
      .done(function( msg ) {
         if(msg == "Done"){
           alert("Added");
         }else{
           alert("Failed to add permission");
         }
      });
        });

    </script>