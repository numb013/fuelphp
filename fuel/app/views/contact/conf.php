<div id="main" class="col-sm-10">

<p class="text-center">以下の内容でよければ、送信お願いします。</p>

<?php echo Form::open(array('action'=>'/contact/send','class' => 'form-horizontal'));?>

<div class="form-group">
    <label for="form_name" class="col-sm-4 control-label">名前</label>
    <div class="col-sm-8">
    	<p class="control-label" style="text-align: left;"><?php echo $name;?></p>

    </div>
  </div>

  <div class="form-group">
    <label for="" class="col-sm-4 control-label">Email</label>
    <div class="col-sm-8">
   	<p class="control-label" style="text-align: left;"><?php echo $email;?></p>

    </div>
  </div>

<div class="form-group">
    <label for="" class="col-sm-4 control-label">内容</label>
    <div class="col-sm-8">
   	<p class="control-label" style="text-align: left;"><?php echo nl2br($msg);?></p>

    </div>
  </div>

  <div class="form-group">
    <div class="col-sm-offset-4 col-sm-8">
		<?php echo Form::submit('submit', '送信する', array('class' => 'btn btn-success'));?>&nbsp;&nbsp;
		<?php echo Form::submit('back', '修正', array('class' => 'btn btn-default'));?>

      <?php echo Form::hidden(Config::get('security.csrf_token_key'), Security::fetch_token());?>
    </div>
  </div>
<?php echo Form::close();?>
