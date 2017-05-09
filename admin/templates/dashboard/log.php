
  <link rel="stylesheet" href="<?php LoadStatic(); echo GetStaticFile('board','board.css')?>">

        <main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">
          <h1>Log</h1>          
          <div class="table-responsive">
          
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Log</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $db = new Model();
                $db->prepare("SELECT Users.username, Log.Logtext, Log.LogDate FROM Log JOIN Users on Users.idUsers=Log.idUser ORDER BY Log.LogDate DESC");
                $result = $db->GetAll();
                foreach($result as $item):
                ?>
                <tr style="cursor: default;">
                  <?php foreach($item as $value):?>
                    <td><?php echo $value ?></td>
                  <?php endforeach;?>
                </tr>
                <?php endforeach;?>
              </tbody>
            </table>
            </div>
        </main>
      </div>
    </div>
    <script>
    
    </script>