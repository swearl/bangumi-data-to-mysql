<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>bangumi-data to mysql</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
<link href="https://cdn.jsdelivr.net/npm/vuetify/dist/vuetify.min.css" rel="stylesheet">
</head>
<body>
<div id="app">
	<v-app>
		<v-toolbar>
			<v-toolbar-title>{{title}}</v-toolbar-title>
		</v-toolbar>
		<v-container>
			<v-layout row>
				<v-flex md8 offset-md2>
					<v-card>
						<v-card-title v-if="version_checking">
							<h3>版本检查中...</h3>
						</v-card-title>
						<div v-else>
							<v-card-title>
								<h3>本地版本: {{local_version}}</h3>
							</v-card-title>
							<v-card-title>
								<h3>在线版本: {{online_version}}</h3>
							</v-card-title>
						</div>
						<v-card-text v-if="updating">
							<h4>{{updating_text}}</h4>
							<v-progress-linear color="primary" height="20" :value="progress"></v-progress-linear>
						</v-card-text>
						<v-card-actions>
							<v-btn color="primary" :loading="version_checking" :disabled="updating || version_checking" @click="checkVersion">重新检查<span slot="loader">检查中...</span></v-btn>
							<v-btn v-if="need_update" color="primary" :loading="updating" :disabled="updating || version_checking" @click="startUpdate">更新<span slot="loader">更新中...</span></v-btn>
						</v-card-actions>
					</v-card>
				</v-flex>
			</v-layout>
		</v-container>
	</v-app>
</div>
<script src="https://cdn.bootcss.com/vue/2.5.16/vue.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vuetify/dist/vuetify.js"></script>
<script src="https://cdn.bootcss.com/axios/0.18.0/axios.min.js"></script>
<script>
axios.defaults.baseURL = "<?=site_url('v2/api/');?>";
axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';

let apiRequest = (api, data, callback) => {
	if(data == undefined) {
		data = {};
	}
	axios.post(api, data).then((res) => {
		if(callback != undefined) {
			callback(res.data);
		}
	});
};

var app = new Vue({
	el: "#app",
	data () {
		return {
			title: "bangumi-data to mysql",
			local_version: "",
			online_version: "",
			version_checking: true,
			need_update: false,
			updating: false,
			downloading: false,
			updating_text: "",
			progress: 0,
			update_filesize: 0,
			downloaded_filesize: 0
		}
	},
	mounted () {
		this.checkVersion();
	},
	methods: {
		checkVersion () {
			this.version_checking = true;
			apiRequest("get_versions", {}, ({ status, msg, data }) => {
				this.version_checking = false;
				this.local_version = data.local_version;
				this.online_version = data.online_version;
				if(this.local_version != this.online_version) {
					this.need_update = true;
				}
			});
		},
		startUpdate () {
			this.updating = true;
			this.downloading = true;
			this.downloaded_filesize = 0;
			this.updating_text = "开始更新";
			apiRequest("prepare_download", {}, ({ status, msg, data }) => {
				if(status == 1) {
					this.updating_text = "data.json文件下载中...";
					this.update_filesize = data.filesize;
					// this.progress = Math.ceil(this.downloaded_filesize / this.update_filesize * 100);
					apiRequest("start_download", {}, ({ status, msg, data }) => {
						if(status == 1) {
							this.downloading = false;
							this.progress = 0;
							this.updating_text = "下载完毕, 开始更新数据库";
							this.updateDB();
							this.checkUpdateDB();
						} else {
							alert(msg);
						}
					});
					this.checkSize();
				} else {
					alert(msg);
				}
			});
		},
		checkSize () {
			apiRequest("check_filesize", {}, ({ status, msg, data }) => {
				if(status == 1) {
					if(this.downloading) {
						this.downloaded_filesize = data.filesize;
						this.progress = Math.ceil(this.downloaded_filesize / this.update_filesize * 100);
						this.updating_text = "data.json文件下载中...(" + this.progress + "%)";
						setTimeout(() => {
							if(this.downloading) {
								this.checkSize();
							}
						}, 300);
					}
				} else {
					alert(msg);
				}
			});
			// console.log("checking");
		},
		updateDB () {
			apiRequest("update_db", {}, ({ status, msg, data }) => {
				if(status == 1) {
					this.progress = 100;
					this.updating_text = "更新完毕";
					this.updating = false;
					this.local_version = this.online_version;
					this.need_update = false;
				} else {
					alert(msg);
				}
			});
		},
		checkUpdateDB () {
			apiRequest("check_db_updating", {}, ({ status, msg, data }) => {
				if(status == 1) {
					if(this.updating) {
						if(data.progress > this.progress) {
							this.progress = data.progress;
						}
						this.updating_text = "数据库更新中...(" + this.progress + "%)";
						setTimeout(() => {
							if(this.updating) {
								this.checkUpdateDB();
							}
						}, 1000);
					}
				} else {
					alert(msg);
				}
			});
		}
	}
});
</script>
</body>
</html>