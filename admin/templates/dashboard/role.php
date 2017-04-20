
  <link rel="stylesheet" href="<?php LoadStatic(); echo GetStaticFile('board','board.css')?>">

        <main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">
          <h1>Rolebased access system</h1>          
          <h2>Change permission of each role</h2>
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
          <form>
            <?php
            ?>
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
    </script>