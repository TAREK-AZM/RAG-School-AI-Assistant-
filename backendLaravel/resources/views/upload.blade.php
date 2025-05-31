<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Upload Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --border-radius: 0.5rem;
            --box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
            color: #1f2937;
        }
        
        .container {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            font-size: 1.25rem;
            color: #111827;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #374151;
        }
        
        .form-control {
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
            padding: 0.625rem 0.75rem;
            font-size: 0.95rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        
        .form-control.is-invalid {
            border-color: var(--danger-color);
        }
        
        .text-muted {
            color: #6b7280 !important;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 0.375rem;
            font-weight: 500;
            padding: 0.625rem 1.25rem;
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
        }
        
        .progress-container {
            margin: 1.5rem 0;
        }
        
        .progress {
            height: 0.5rem;
            border-radius: 1rem;
            background-color: #e5e7eb;
            margin-bottom: 0.5rem;
            overflow: hidden;
        }
        
        .progress-bar {
            background-color: var(--primary-color);
            border-radius: 1rem;
            transition: width 0.3s ease;
        }
        
        .progress-text {
            font-size: 0.875rem;
            color: #6b7280;
            text-align: right;
        }
        
        #upload-status {
            display: none;
            margin-top: 1.5rem;
            border-radius: var(--border-radius);
            padding: 1.25rem;
            animation: fadeIn 0.3s ease-in-out;
        }
        
        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #065f46;
        }
        
        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #b91c1c;
        }
        
        #document-details {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .file-input-wrapper {
            position: relative;
            margin-bottom: 1rem;
        }
        
        .file-input-wrapper .form-control {
            padding-right: 7rem;
        }
        
        .file-count {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background-color: #e5e7eb;
            color: #4b5563;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .container {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }
            
            .card-header {
                padding: 1rem;
                font-size: 1.125rem;
            }
            
            .card-body {
                padding: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card">
                    <div class="card-header">
                        <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: -5px;">
                            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <path d="M12 18v-6"></path>
                            <path d="M8 15h8"></path>
                        </svg>
                        Upload Document
                    </div>
                    <div class="card-body">
                        <form id="uploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <label for="title" class="form-label">Document Title</label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Enter document title" required>
                                <div class="invalid-feedback" id="title-error"></div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="category" class="form-label">Category (Optional)</label>
                                <input type="text" class="form-control" id="category" name="category" placeholder="Enter category">
                                <div class="invalid-feedback" id="category-error"></div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="documents" class="form-label">Document Files</label>
                                <div class="file-input-wrapper">
                                    <input type="file" multiple class="form-control" id="documents" name="documents[]" required>
                                    <span class="file-count" id="file-count">No files</span>
                                </div>
                                <div class="invalid-feedback" id="document-error"></div>
                                <small class="text-muted">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: -3px; margin-right: 4px;">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                    </svg>
                                    Accepted formats: PDF, DOC, DOCX, TXT (Max: 10MB)
                                </small>
                            </div>
                            
                            <div class="progress-container" style="display: none;">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <div class="progress-text">0%</div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: -3px; margin-right: 6px;">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    Upload Document
                                </button>
                            </div>
                        </form>
                        
                        <div id="upload-status" class="alert" role="alert">
                            <h5 id="status-message" class="mb-2 fw-bold"></h5>
                            <div id="document-details"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Update file count when files are selected
            $('#documents').on('change', function() {
                const fileCount = this.files.length;
                $('#file-count').text(fileCount > 0 ? fileCount + ' file' + (fileCount > 1 ? 's' : '') + ' selected' : 'No files');
            });
        
            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();
                
                // Reset error messages
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#upload-status').hide();
                
                let formData = new FormData(this);
                
                $.ajax({
                    url: '{{ route("school-assistant.upload") }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = ((evt.loaded / evt.total) * 100);
                                $('.progress-container').show();
                                $('.progress-bar').width(percentComplete + '%');
                                $('.progress-text').text(Math.round(percentComplete) + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    beforeSend: function() {
                        $('#upload-status').hide();
                        $('.progress-container').show();
                        $('button[type="submit"]').prop('disabled', true).html(`
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span class="ms-2">Uploading...</span>
                        `);
                    },
                    success: function(response) {
                        $('button[type="submit"]').prop('disabled', false).html(`
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: -3px; margin-right: 6px;">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            Upload Document
                        `);
                        
                        $('#upload-status').removeClass('alert-danger').addClass('alert-success').show();
                        $('#status-message').html(`
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: -4px; margin-right: 8px;">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            ${response.message}
                        `);
                        
                        let details = `
                            <div class="mt-3">
                                <div class="mb-2"><strong>Title:</strong> ${response.document.title}</div>
                                <div class="mb-2"><strong>Category:</strong> ${response.document.category || 'None'}</div>
                                <div class="d-flex align-items-center">
                                    <span class="me-2"><strong>Status:</strong></span>
                                    <span class="badge bg-warning text-dark">Processing...</span>
                                </div>
                            </div>
                        `;
                        
                        $('#document-details').html(details);
                        $('#uploadForm')[0].reset();
                        $('#file-count').text('No files');
                        $('.progress-container').hide();
                    },
                    error: function(xhr) {
                        $('button[type="submit"]').prop('disabled', false).html(`
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: -3px; margin-right: 6px;">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            Upload Document
                        `);
                        $('.progress-container').hide();
                        
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key + '-error').text(value[0]);
                            });
                            
                            $('#upload-status').removeClass('alert-success').addClass('alert-danger').show();
                            $('#status-message').html(`
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: -4px; margin-right: 8px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                There were errors with your submission.
                            `);
                            $('#document-details').html('');
                        } else {
                            $('#upload-status').removeClass('alert-success').addClass('alert-danger').show();
                            $('#status-message').html(`
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: -4px; margin-right: 8px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                An error occurred while uploading your document.
                            `);
                            $('#document-details').html('Please try again later.');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>