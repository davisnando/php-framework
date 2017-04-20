
  <link rel="stylesheet" href="<?php LoadStatic(); echo GetStaticFile('board','board.css')?>">

        <main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">
          <h1>Rolebased access system</h1>  
          <h2>Create role</h2>
          <form id="createRole">
            <input type="text" id="NameRole" class="form-control" placeholder="Name of Role"value="" required>
            <input type="submit" class="form-control" value="Create">
          </form>
          <hr>        
          <h2>Change permission of each role</h2>
          <label >Select Role:</label>
          <select id="Role" class="form-control">
          <option value=""></option>

          <?php 
          $db = new Model();
          $db->prepare("SELECT idRole,name from Role");
          $result = $db->GetAll();
          foreach($result as $column):
          ?>

            <option value="<?php echo $column['idRole'] ?>"><?php echo $column['name'] ?></option>
          <?php endforeach; ?>
          </select>
          <br>
          <div id="boxes">
            <?php
            $db->prepare("SELECT * FROM Perm");
            $result = $db->GetAll();
            foreach($result as $column):
            ?>
            <div class="checkbox">
           <label><input type="checkbox" class="permission" id="perm<?php echo $column['idPerm'];?>" value="<?php echo $column['idPerm']; ?>"> <?php echo $column['description']; ?></label>
            </div>

            <?php endforeach; ?>
          </div>
          <hr>
          <h2>Change role from user</h2>
          <form id="ChangeRole">
          <label >Select User:</label>
          <select id="UserChange" class="form-control" placeholder="">
            <option value=""></option>

            <?php
              $db->prepare("SELECT idUsers,username FROM Users JOIN userRole on userRole.idUser=Users.idUsers WHERE userRole.idRole <> 1");
              $result = $db->GetAll();
              foreach($result as $item):
            ?>
            <option id="item<?php echo $item['idUsers']; ?>" value="<?php echo $item['idUsers']; ?>"><?php echo $item['username'];  ?></option>
            <?php endforeach;?>
          </select>
          <label >Select Role:</label>
          <select id="RoleChange" class="form-control">
          <option value=""></option>
          <?php 
          $db->prepare("SELECT idRole,name from Role");
          $result = $db->GetAll();
          foreach($result as $column):
          ?>

            <option value="<?php echo $column['idRole'] ?>"><?php echo $column['name'] ?></option>
          <?php endforeach; ?>
          </select>
          <input type="submit" class="form-control" value="Change">
          </form>
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
    </script>