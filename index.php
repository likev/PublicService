<!doctype html>

<html>

	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
		<meta charset="utf-8">
		<meta content=" 河南省气象局关于建立全省公共气象服务产品库的通知" name="description"/>
	
		<title>上传-全省公共气象服务产品库</title>

		<style>
			#browser-tips{
				display:none;
			}
			
			header{
				margin:30px 10px;
			}
			
			select{
				font-size:1.2em;
				padding: 5px 6px;
			}
			select option{
				 padding: 0 6px 5px;
			}
			
			#fileElem{
				display:none;
			}
			
			#fileSelect{
				border: 2px solid #808080;
				color: #000000;
				display: block;
				height: 80px;
				padding-top: 50px;
				text-align: center;
				text-decoration: none;
				vertical-align: middle;
				width: 600px;
			}
			
			#fileSelect.dragenter{
				border:2px blue solid;
			}
			
			#filelist-title{
				display:none;
			}
			
			#filelist li{
				margin-top:15px;
			}
			
			#filelist li input{
				padding:5px 10px;
				font-size:1.1em;
				font-family: "Trebuchet MS",Arial,Helvetica,sans-serif;

			}
			
			.template{
				display:none;
			}
			
			.filetype{
				margin-left:20px;
				
			}
			
			#upload{
				display:none;
				font-size:1.3em;
				padding:10px 50px;
			}
			
			footer{
				margin-top:100px;
			}
			
		</style>
		<script src="http://172.18.172.200/czb2/script/jquery-1.9.0.min.js"></script>
		<script src="http://172.18.172.200/czb2/script/moment-1.7.2.min.js"></script>
		
		<script>
			$(function(){
			
				if(!(window.FileReader && window.FormData) ){
					$('#browser-tips').show();
					return false;
				}
			
				$('#fileSelect').click(function(e){
					$('#fileElem').click();
					e.preventDefault();

				});
				
				$('#fileSelect').on('dragenter', function(e) {
					e.stopPropagation();
					e.preventDefault();
					
					$(this).addClass('dragenter');
				})
				
				$('#fileSelect').on('dragleave mouseleave', function(e) {
					
					
					$(this).removeClass('dragenter');
				})

				$('#fileSelect').on('dragover', function(e) {
					e.stopPropagation();
					e.preventDefault();
				})
				
				$('#fileElem').change(function(){
					handleFiles(this.files);
				})
				
				var selectedFiles = [];
				
				function handleFiles(files){
					for (var nFileId = 0; nFileId < files.length; nFileId++) {
						var file = files[nFileId];
						
						var nBytes=file.size, sOutput = nBytes + " bytes";
						  // optional code for multiples approximation
						for (var aMultiples = ["KiB", "MiB", "GiB", "TiB", "PiB", "EiB", "ZiB", "YiB"], nMultiple = 0, nApprox = nBytes / 1024; nApprox > 1; nApprox /= 1024, nMultiple++) {
								sOutput = nApprox.toFixed(3) + " " + aMultiples[nMultiple] + " (" + nBytes + " bytes)";
						}

						//var li = $('<li>'+ file.name + ' ' + sOutput +' ' + file.type + '</li>');
						var li = $('<li><span class="filename">'+ file.name+'</span></li>');
						
						var filetype = $('.template').clone().removeClass('template');
						filetype.appendTo(li);
						
						var name_input = $('<br><input class="newname" type=text maxlength="100" size="70" >');
						li.append(name_input);
						$('#filelist').append(li);
						
						filetype.change();

						selectedFiles.push(file);
					}
					
					if(selectedFiles.length) $('#filelist-title, #upload').show();
				}
				
				$('#fileSelect').on('drop', function(e) {
					e.stopPropagation();
					e.preventDefault();

					var dt = e.originalEvent.dataTransfer;
					var files = dt.files;
					
					

					handleFiles(files);
				})
				
				function sendFile(file, newname, li){
					var fd = new FormData();
					fd.append('myFile', file);
					fd.append('newname', newname);
					
					var onFail = function(data){
						li.css({color:'red'});
						alert('传输失败，请点击上传按钮重试！');
					}

					$.ajax({
					  url: "upload.php",
					  type: "POST",
					  data: fd,
					  processData: false,  // 告诉jQuery不要去处理发送的数据
					  contentType: false   // 告诉jQuery不要去设置Content-Type请求头
					})
					 .done(function(data){
						if(data === 'success'){
							li.css({color:'blue'});
							
							var filename = li.children('.filename');
							filename.after('<span class="upload-state"> —— 上传成功！</span>');
						}else{
							//ftp fail
							onFail(data);
						}
					 })
					 .fail(onFail);

				}
				
				function getFileName(city, type3){
					var type1 = 'MSP1',
						element = 'ME',
						height = 'L88', // 地面或水面
						area = 'SNO', //HA 河南省 R2 不确定区域 ST单站
						begin_time = moment().hours(20).minutes(0).format('YYYYMMDDHHmm'), 
						span_time = 'D0000-D0001'; 
						
					if(type3 === 'WF' || type3 === 'WFDWS' || type3 === 'WF-WEEK'){
						type1 = 'MSP2';
					}else if(type3 === 'TPPIFC' || type3 === 'MDHMS'){
						type1 = 'MSP3';
					}
					
					if(type3 === 'AWS'){
						span_time = 'D0001-D0000';
					
					}else if(type3 === 'WF'){
						span_time = 'D0000-D0002';
					
					}else if(type3 === 'WF-WEEK'){
						type3 = 'WF';//修改自定义类型至标准类型
						element = 'WEEK';
						span_time = 'W0000-W0001';
					
					}else if(type3 === 'TPPIFC'){
					
					}else if(type3 === 'WFDWS'){
					
					}else if(type3 === 'MDHMS'){
					
					}
					
					return 	type1 + '_' + city + '_' + type3  + '_' + element  + '_' + height + '_' + area  + '_' + begin_time + '_' + span_time;
				}
				
				$('#upload').click(function(){
					var names = $('#filelist .newname');
					for(var index=0; index<selectedFiles.length; index++){
						var input = names.eq(index);
						var newname = input.val();
						sendFile(selectedFiles[index], newname, input.parent());
					}
				});

				$('#filelist').on('change','.filetype', function(){
					
					var oname = $(this).siblings('.filename').text();
					
					var ext = oname.substr(oname.lastIndexOf('.') ).toUpperCase();
					if(ext === '.05' || ext === '.06' || ext === '.15' || ext === '.16'){
						ext = '.TXT';//修改自定义后缀为标准类型
					}
					
					var filename = getFileName($('#select-city').val(), $(this).val() );
					
					$(this).siblings('.newname').val(filename+ext);
				});
				
				$('#select-city').change(function(){
					$('.filetype').change();//修改所有文件名
				})
			});
		</script>
	</head>

	<body>
	
	<header>
	<div id='browser-tips'>
		<p>你的浏览器太落后了，网站不能正常运行！</p>
		<p>推荐使用：<a href="http://firefox.com.cn/" title="点击打开Firefox浏览器下载页面">Firefox</a>或者<a href="https://www.google.com/intl/zh-CN/chrome/browser/" title="点击打开Chrome浏览器下载页面">Chrome</a>浏览器。</p>
		<p>也可使用下面浏览器的最新版：<a href="http://www.maxthon.cn/" title="点击打开傲游浏览器下载页面">傲游浏览器</a>、<a href="http://ie.sogou.com/" title="点击打开搜狗浏览器下载页面">搜狗浏览器</a>或者<a href="http://se.360.cn/" title="点击打开360浏览器下载页面">360浏览器</a>。</p>
	</div>
	<label for='select-city'>请选择地市</label>
	<select id="select-city">
	<option value="BFLB">洛阳市</option>
	<option value="BFSF">三门峡市</option>
	<option value="BFZZ">郑州市</option>
	<option value="BFKF">开封市</option>
	<option value="BFXC">许昌市</option>
	<option value="BFSQ">商丘市</option>
	<option value="BFPS">平顶山市</option>
	<option value="BFZK">周口市</option>
	<option value="BFLE">漯河市</option>
	<option value="BFNY">南阳市</option>
	<option value="BFZM">驻马店市</option>
	<option value="BFXI">信阳市</option>
	<option value="BFAY">安阳市</option>
	<option value="BFHI">鹤壁市</option>
	<option value="BFPY">濮阳市</option>
	<option value="BFJT">焦作市</option>
	<option value="BFXX">新乡市</option>
	</select>
	
	</header>
		
	<input type="file" id="fileElem" multiple >
	<a href="#" id="fileSelect">点击选择文件 或 将文件拖入此框</a>
	
	<div>
	<h2 id='filelist-title'>已选择文件列表</h2>
	<select class="filetype template">
		<option value="AWS">雨情</option>
		<option value="WF">天气预报</option>
		<option value="WF-WEEK">周天气预报</option>
		<option value="TPPIFC">景区天气预报</option>
		<option value="WFDWS">预警信号</option>
		<option value="WF">重要天气预报</option>
		<option value="MDHMS">专题(或节假日)气象服务</option>
	</select>
	<ol id='filelist'>
	</ol>
	<button id="upload">上传</button>
	</div>
	
	<footer>
	<h2>网站说明：</h2>
	<ol>
		<li>此网站目的为方便预报员上传气象服务产品至省级服务器，仅供地市局气象台使用(不支持省级和县局用户)</li>
		<li>此网站采用最新的网站技术，不支持IE11以下浏览器及其他旧版浏览器</li>
		<li>使用时请首先选择所在地市</li>
		<li>可一次选择并上传多个文件</li>
		<li>网站自动转换的文件名可能不正确，在上传前可手工修改</li>
		<li>洛阳市气象台版权所有 其他问题请电子邮件联系 xu_work@qq.com</li>
	</ol>
	</footer>
	
	</body>

</html>