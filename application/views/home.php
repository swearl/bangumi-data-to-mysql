<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view("include/header");
?>

<div class="jumbotron jumbotron-fluid">
	<div class="container">
		<h1>bangumi-data to mysql</h1>
		<hr class="my-4">
		<p>本地版本: <?php echo $version_local;?> SHA: <?php echo $sha_local;?></p>
		<p>在线版本: <?php echo $version_online;?> SHA: <?php echo $sha_online;?></p>
		<?php if($version_local != $version_online):?>
		<p class="lead">
			<a href="#" class="btn btn-primary btn-lg" role="button" id="btn-update">更新</a>
		</p>
		<?php endif;?>
	</div>
	<div class="container" id="container-output" hidden>
		<p id="update-status">开始更新</p>
		<div class="progress">
			<div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
		</div>
	</div>
</div>

<?php
$this->load->view("include/footer_js");
?>

<script>
var update_files = [];
var update_files_count = 0;
var updating_index = 0;
var version_online = "<?php echo $version_online;?>";
var sha_online = "<?php echo $sha_online;?>";

function update_file() {
	update_progress();
	if(updating_index < update_files_count) {
		var file = update_files[updating_index];
		display_status(file + " 更新中...");
		$.ajax({
			url: "<?php echo site_url('api/update_file');?>",
			data: {
				filename: file,
				version: version_online
			},
			type: "post",
			dataType: "json",
			success: function(json) {
				if(json.status == 1) {
					// console.log(json);
					updating_index++;
					if(updating_index < update_files_count) {
						update_file();
					} else {
						update_complete();
					}
				} else if(json.status == -1) {
					alert("更新文件格式有误");
				} else {
					if(confirm(file + "文件更新时出现错误, 是否重试?")) {
						update_file();
					}
				}
			}
		});
		// console.log(file);
		// updating_index++;
		// update_file();
	}
	// display_status("更新完毕");
}

function display_status(status) {
	$("#update-status").html(status);
}

function update_progress() {
	var display = updating_index + "/" + update_files_count;
	var percent = Math.floor(updating_index / update_files_count * 100);
	$("#container-output .progress-bar").html(display).attr("aria-valuenow", percent).css("width", percent + "%");
	// console.log(percent);
}

function update_complete() {
	update_progress();
	$.ajax({
		url: "<?php echo site_url('api/update_complete');?>",
		data: {
			sha: sha_online,
			version: version_online
		},
		type: "post",
		dataType: "json",
		success: function(json) {
			if(json.status == 1) {
				display_status("更新完毕");
			}
		}
	});
}

$(function() {
	$("#btn-update").click(function() {
		if($(this).hasClass("disabled")) {
			return false;
		}
		$(this).addClass("disabled");
		$("#container-output").removeAttr("hidden");
		display_status("正在查找更新文件...");
		var _self = $(this);
		$.ajax({
			url: "<?php echo site_url('api/get_update_files');?>",
			type: "post",
			dataType: "json",
			success: function(json) {
				// _self.removeClass("disabled");
				// console.log(json);
				update_files = json;
				update_files_count = update_files.length;
				update_file();
			},
			error: function() {
				alert("查找更新文件时出错, 请重试");
				_self.removeClass("disabled");
			}
		});
		return false;
	});
});
</script>
<?php
$this->load->view("include/footer");
