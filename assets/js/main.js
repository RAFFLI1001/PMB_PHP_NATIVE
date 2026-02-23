/**
 * Main JavaScript untuk PMB UTN
 */

$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Enable tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Enable popovers
    $('[data-bs-toggle="popover"]').popover();
    
    // Confirm before delete
    $('.confirm-delete').on('click', function() {
        return confirm('Apakah Anda yakin ingin menghapus data ini?');
    });
    
    // Form validation
    $('form.needs-validation').on('submit', function(event) {
        if (this.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        $(this).addClass('was-validated');
    });
    
    // Auto-format phone number
    $('.phone-format').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length > 3 && value.length <= 6) {
            value = value.replace(/(\d{3})(\d{1,3})/, '$1-$2');
        } else if (value.length > 6 && value.length <= 9) {
            value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1-$2-$3');
        } else if (value.length > 9) {
            value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{1,4})/, '$1-$2-$3-$4');
        }
        $(this).val(value);
    });
    
    // Auto-capitalize names
    $('.capitalize').on('blur', function() {
        var words = $(this).val().toLowerCase().split(' ');
        for (var i = 0; i < words.length; i++) {
            words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1);
        }
        $(this).val(words.join(' '));
    });
    
    // Character counter for textarea
    $('textarea[maxlength]').each(function() {
        var maxLength = $(this).attr('maxlength');
        var counter = $('<small class="text-muted float-end"><span class="char-count">0</span>/' + maxLength + '</small>');
        $(this).after(counter);
        
        $(this).on('input', function() {
            var currentLength = $(this).val().length;
            $(this).next('.char-count').text(currentLength);
            
            if (currentLength >= maxLength) {
                $(this).next().addClass('text-danger').removeClass('text-muted');
            } else {
                $(this).next().addClass('text-muted').removeClass('text-danger');
            }
        }).trigger('input');
    });
    
    // Auto-save form data
    var autosaveTimer;
    $('form.auto-save').on('input', function() {
        clearTimeout(autosaveTimer);
        autosaveTimer = setTimeout(function() {
            var formData = $(this).serialize();
            localStorage.setItem('autosave_' + $(this).attr('id'), formData);
            console.log('Form data autosaved');
        }.bind(this), 2000);
    });
    
    // Load autosaved data
    $('form.auto-save').each(function() {
        var savedData = localStorage.getItem('autosave_' + $(this).attr('id'));
        if (savedData) {
            // Convert saved data to form values
            var params = new URLSearchParams(savedData);
            params.forEach(function(value, key) {
                var element = $(this).find('[name="' + key + '"]');
                if (element.length) {
                    if (element.attr('type') === 'checkbox' || element.attr('type') === 'radio') {
                        element.filter('[value="' + value + '"]').prop('checked', true);
                    } else {
                        element.val(value);
                    }
                }
            }.bind(this));
            console.log('Autosaved data loaded');
        }
    });
    
    // Clear autosaved data on successful submit
    $('form.auto-save').on('submit', function() {
        localStorage.removeItem('autosave_' + $(this).attr('id'));
    });
    
    // Image preview for file inputs
    $('input[type="file"][accept="image/*"]').on('change', function() {
        var input = this;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                // Remove existing preview
                $(input).next('.image-preview').remove();
                
                // Create preview container
                var preview = $('<div class="image-preview mt-2"></div>');
                var img = $('<img class="img-thumbnail" style="max-height: 200px;">');
                img.attr('src', e.target.result);
                
                // Add remove button
                var removeBtn = $('<button type="button" class="btn btn-sm btn-danger mt-2">Hapus</button>');
                removeBtn.on('click', function() {
                    $(input).val('');
                    preview.remove();
                });
                
                preview.append(img).append(removeBtn);
                $(input).after(preview);
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    });
    
    // Print functionality
    $('.print-btn').on('click', function() {
        window.print();
    });
    
    // Copy to clipboard
    $('.copy-btn').on('click', function() {
        var text = $(this).data('copy');
        navigator.clipboard.writeText(text).then(function() {
            var originalText = $(this).html();
            $(this).html('<i class="fas fa-check"></i> Tersalin!');
            setTimeout(function() {
                $(this).html(originalText);
            }.bind(this), 2000);
        }.bind(this));
    });
    
    // Smooth scroll to top
    $('.scroll-top').on('click', function() {
        $('html, body').animate({ scrollTop: 0 }, 500);
    });
    
    // Show scroll-to-top button
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 300) {
            $('.scroll-top').fadeIn();
        } else {
            $('.scroll-top').fadeOut();
        }
    });
});

// Utility functions
function formatDate(dateString) {
    var date = new Date(dateString);
    var options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    return date.toLocaleDateString('id-ID', options);
}

function formatCurrency(amount) {
    return 'Rp ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function validateNIK(nik) {
    var regex = /^\d{16}$/;
    return regex.test(nik);
}

function showLoading(message) {
    $('#loadingModal .modal-body').html(message || 'Memproses...');
    $('#loadingModal').modal('show');
}

function hideLoading() {
    $('#loadingModal').modal('hide');
}

// AJAX helper
function ajaxRequest(url, data, method, callback) {
    $.ajax({
        url: url,
        type: method || 'POST',
        data: data,
        dataType: 'json',
        beforeSend: function() {
            showLoading();
        },
        success: function(response) {
            callback(response);
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        },
        complete: function() {
            hideLoading();
        }
    });
}