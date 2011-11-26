document.addEventListener('DOMContentLoaded', function() {
	var noFiles = document.getElementById('nofiles'),
		addRow = function(file)
		{
			var files = document.getElementById('files'),
				row = document.createElement('tr'),
				nameCell = document.createElement('td'),
				downloadLink = document.createElement('a'),
				typeCell = document.createElement('td'),
				sizeCell = document.createElement('td'),
				uploadedCell = document.createElement('td'),
				controlsCell = document.createElement('td'),
				delButton = document.createElement('a');
				
			if (noFiles && noFiles.parentNode)
			{
				noFiles.parentNode.removeChild(noFiles);
			}
			
			delButton.className = 'delete';
			delButton.dataset['id'] = file.file_id;
			delButton.href = '#delete';
			delButton.appendChild(document.createTextNode('delete'));
			controlsCell.appendChild(delButton);
			uploadedCell.appendChild(document.createTextNode(file.created));
			sizeCell.appendChild(document.createTextNode(file.size));
			typeCell.appendChild(document.createTextNode(file.mime_type));
			downloadLink.appendChild(document.createTextNode(file.name));
			downloadLink.href = '/stor.me/files/download/?id=' + encodeURIComponent(file.file_id);
			nameCell.appendChild(downloadLink);
			
			row.appendChild(nameCell);
			row.appendChild(typeCell);
			row.appendChild(sizeCell);
			row.appendChild(uploadedCell);
			row.appendChild(controlsCell);
			row.id = 'file-' + file.file_id;
			row.className = 'updated';
			
			setTimeout(function() {
				row.className = 'row';
			}, 5000);
			
			files.insertBefore(row, files.firstChild);
			
			updateFileCount(files.getElementsByTagName('tr').length);
		},
		showUpdated = function(updated)
		{
			var row = document.getElementById('file-' + updated.file_id),
				files = document.getElementById('files');
			
			row.className ='updated';
			files.insertBefore(row, files.firstChild);
				
			setTimeout(function() {
				row.className = 'row';
			}, 3000);
		},
		showError = function(error)
		{
			
		},
		showSuccess = function(success)
		{
		},
		updateFileCount = function(count)
		{
			var elem = document.getElementById('fileCount');
			elem.removeChild(elem.firstChild);
			elem.appendChild(document.createTextNode(count));
		},
		progress = {
			start: function(file)
			{
				var reader = new FileReader(),
					xhr = new XMLHttpRequest(),
					self = this,
					nameElem = document.createElement('div'),
					progressElem = document.createElement('div'),
					progressBar = document.createElement('div'),
					data = new FormData(),
					i;
			
				this.elem = document.createElement('div');
				this.elem.className = 'upload';
				nameElem.appendChild(document.createTextNode(file.name));
				progressElem.appendChild(progressBar);
				progressElem.className = 'progressBar';
				progressBar.style.width = '0%';
				this.elem.appendChild(nameElem);
				this.elem.appendChild(progressElem);
				
				xhr.upload.addEventListener('progress', function(e) {
					if (e.lengthComputable)
					{
						var percentage = Math.round((e.loaded * 100) / e.total);
						progressBar.style.width = percentage + '%';
					}
				}, false);
				
				xhr.upload.addEventListener('load', function(e) {
					progressBar.className = 'complete';
				}, false);
				
				xhr.onreadystatechange = function()
				{
					if (xhr.readyState == 4 && xhr.status === 200)
					{
						var response = JSON.parse(xhr.responseText);
						
						if (response.updated)
						{
							showUpdated(response.updated);
						}
						else if (response.error)
						{
							showError(response.error);
						}
						else
						{
							addRow(JSON.parse(xhr.responseText));
						}
					}
				}
				
				xhr.open('POST', '/stor.me/files/upload');
				xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');
				data.append('token', document.getElementById('token').value);
				data.append('file', file);
				xhr.send(data);
			}
		};

	document.addEventListener('dragenter', function(e) {
		e.stopPropagation();
		e.preventDefault();
		return false;
	});

	document.addEventListener('dragover', function(e) {
		e.stopPropagation();
		e.preventDefault();
		return false;
	});

	document.addEventListener('drop', function(e) {
		var dt = e.dataTransfer,
			files = dt.files,
			i,
			counter,
			counters = document.getElementById('counters');
		
		if (files.length > 0)
		{
			counters.style.display = 'block';
		}
		
		for (i = 0; i < files.length; i++)
		{
			counter = Object.create(progress);
			counter.start(files[i]);
			counters.appendChild(counter.elem);
		}

		e.stopPropagation();
		e.preventDefault();
		return false;
	});
	
	document.addEventListener('click', function(e) {
		var target = e.target,
			xhr,
			id,
			data;
		
		if (target.className === 'delete')
		{
			data = new FormData();
			data.append('id', target.dataset['id']);
			data.append('token', document.getElementById('token').value);
		
			xhr = new XMLHttpRequest();
			
			xhr.onreadystatechange = function()
			{
				var row, obj, parent, rowCount, cell;
				if (xhr.readyState === 4 && xhr.status === 200)
				{
					obj = JSON.parse(xhr.responseText);
					
					if (obj.error)
					{
						showError(obj.error);
					}
					else if (obj.deleted)
					{
						row = document.getElementById('file-' + obj.deleted.file_id);
						parent = row.parentNode;
						parent.removeChild(row);
						rowCount = parent.getElementsByTagName('tr').length;
						updateFileCount(rowCount);
						if (rowCount === 0)
						{
							if (!noFiles)
							{
								noFiles = document.createElement('tr');
								noFiles.id = 'nofiles';
								cell = document.createElement('td');
								cell.setAttribute('colspan', '5');
								cell.appendChild(document.createTextNode('No stuff! Upload some!'));
								noFiles.appendChild(cell);
							}
							
							parent.appendChild(noFiles);
						}
						showSuccess('Removed file ' + obj.deleted.name);
					}
				}
			}
			
			xhr.open('POST', '/stor.me/files/delete');
			xhr.send(data);
			e.preventDefault();
			return false;
		}
	});
}, false);