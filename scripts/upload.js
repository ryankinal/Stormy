document.addEventListener('DOMContentLoaded', function() {
    var WEB_ROOT = '/stor.me',
        noFiles = document.getElementById('nofiles'),
        fileSize = function(size, truncate)
        {
            truncate = truncate || 1;
            
            var sz = ['B','K','M','G','T','P'],
                factor = Math.floor((size.toString().length - 1) / 3),
                divisor = Math.pow(1024, factor),
                str = '' + (size / divisor);
                
            return str.substring(0, str.indexOf('.') + truncate + 1) + sz[factor];
        },
        fileType = function(type)
        {
            var conversions = {
                'octet-stream': 'binary'
            };
            
            type = type.substring(type.indexOf('/') + 1);
            
            return (conversions[type]) ? conversions[type] : type;
        },
        addRow = function(file)
        {
            var files = document.getElementById('files'),
                row = document.createElement('article'),
                downloadLink = document.createElement('a'),
                nameCell = document.createElement('div'),
                statsCell = document.createElement('div'),
                typeCell = document.createElement('div'),
                sizeCell = document.createElement('div'),
                uploadedCell = document.createElement('div'),
                controlsCell = document.createElement('div'),
                delButton = document.createElement('a'),
                date = new Date(file.created),
                dateString = (date.getUTCMonth() + 1) + '/' + date.getUTCDate() + '/' + (date.getUTCFullYear() - 2000);
                
            if (noFiles && noFiles.parentNode)
            {
                noFiles.parentNode.removeChild(noFiles);
            }
            
            delButton.className = 'delete';
            delButton.dataset['id'] = file.file_id;
            delButton.href = '#delete';
            delButton.textContent = 'delete';
            controlsCell.appendChild(delButton);
            
            uploadedCell.textContent = dateString;
            sizeCell.textContent = fileSize(file.size);
            typeCell.textContent = fileType(file.mime_type);
            downloadLink.textContent = file.name;
            downloadLink.href = WEB_ROOT + '/files/download/?id=' + encodeURIComponent(file.file_id);
            nameCell.appendChild(downloadLink);
            
            controlsCell.className = 'left quarter';
            uploadedCell.className = 'left quarter';
            sizeCell.className = 'left quarter';
            typeCell.className = 'left quarter';
            statsCell.className = 'left three-fifths file-stats';
            nameCell.className = 'left two-fifths file-name';
            
            statsCell.appendChild(typeCell);
            statsCell.appendChild(sizeCell);
            statsCell.appendChild(uploadedCell);
            statsCell.appendChild(controlsCell);
            
            row.appendChild(nameCell);
            row.appendChild(statsCell);
            row.id = 'file-' + file.file_id;
            row.className = 'file-row clear updated';
            
            setTimeout(function() {
                row.className = 'file-row clear';
            }, 5000);
            
            files.insertBefore(row, files.firstChild);
            
            updateFileCount(files.getElementsByTagName('article').length);
        },
        showUpdated = function(updated)
        {
            var row = document.getElementById('file-' + updated.file_id),
                files = document.getElementById('files');
            
            row.className ='file-row clear updated';
            files.insertBefore(row, files.firstChild);
                
            setTimeout(function() {
                row.className = 'file-row clear';
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
            var elem = document.getElementById('fileCount'),
                text = count + ' file' + ((count === 1) ? '' : 's');
            elem.removeChild(elem.firstChild);
            elem.textContent = text;
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
                nameElem.textContent = file.name;
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
                    var parent = self.elem.parentNode;
                    
                    setTimeout(function() {
                        parent.removeChild(self.elem);
                        
                        var files = parent.getElementsByTagName('div');
                        
                        if (files.length === 0)
                        {
                            parent.style.display = 'none';
                        }
                    }, 10000);
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
                        rowCount = parent.getElementsByTagName('article').length;
                        updateFileCount(rowCount);
                        if (rowCount === 0)
                        {
                            if (!noFiles)
                            {
                                noFiles = document.createElement('article');
                                noFiles.id = 'nofiles';
                                noFiles.className = 'file-row clear';
                                noFiles.textContent = 'No stuff! Upload some!';
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