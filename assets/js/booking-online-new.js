// Enhanced Booking Online JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== Enhanced Booking System Loaded ===');
    
    // Booking state
    let bookingState = {
        selectedCourt: null,
        selectedDate: null,
        selectedTime: null,
        selectedDuration: 1,
        totalPrice: 0,
        availableSlots: []
    };

    // Current step
    let currentStep = 1;

    // Initialize
    initializeBookingSystem();

    function initializeBookingSystem() {
        setupCourtSelection();
        setupDateTimeSelection();
        setupPaymentSelection();
        setupNavigation();
        setupFormSubmission();
        
        // Set default date
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('bookingDate').value = today;
        bookingState.selectedDate = today;
        
        // Initialize payment methods
        document.querySelector('.payment-methods').classList.add('has-selection');
    }

    // Court Selection
    function setupCourtSelection() {
        document.querySelectorAll('.court-card').forEach(card => {
            card.addEventListener('click', function() {
                // Remove previous selection
                document.querySelectorAll('.court-card').forEach(c => {
                    c.classList.remove('selected');
                });
                
                // Select this court
                this.classList.add('selected');
                
                // Store court data
                bookingState.selectedCourt = {
                    id: this.dataset.courtId,
                    name: this.dataset.courtName,
                    price: parseInt(this.dataset.courtPrice)
                };
                
                // Enable next button
                document.getElementById('nextToStep2').disabled = false;
                
                // Add animation
                this.style.animation = 'none';
                setTimeout(() => {
                    this.style.animation = 'pulse 0.6s ease';
                }, 10);
                
                console.log('Court selected:', bookingState.selectedCourt);
            });
        });
    }

    // Date & Time Selection
    function setupDateTimeSelection() {
        // Date change handler
        document.getElementById('bookingDate').addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                showAlert('Không thể chọn ngày trong quá khứ', 'warning');
                this.value = today.toISOString().split('T')[0];
                return;
            }
            
            bookingState.selectedDate = this.value;
            loadTimeSlots();
        });

        // Duration change handler
        document.getElementById('duration').addEventListener('change', function() {
            bookingState.selectedDuration = parseInt(this.value);
            loadTimeSlots();
        });
    }

    // Load time slots from API
    function loadTimeSlots() {
        if (!bookingState.selectedCourt || !bookingState.selectedDate) {
            return;
        }

        const container = document.getElementById('timeSlotsContainer');
        container.innerHTML = `
            <div class="col-12 text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-2 text-muted">Đang kiểm tra lịch trống...</p>
            </div>
        `;

        const apiUrl = `api/time-slots.php?court_id=${bookingState.selectedCourt.id}&date=${bookingState.selectedDate}`;
        
        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderTimeSlots(data.data.slots);
                } else {
                    throw new Error(data.error || 'API error');
                }
            })
            .catch(error => {
                console.error('Error loading time slots:', error);
                container.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Không thể tải khung giờ. Vui lòng thử lại.
                        </div>
                    </div>
                `;
            });
    }

    // Render time slots
    function renderTimeSlots(slots) {
        const container = document.getElementById('timeSlotsContainer');
        bookingState.availableSlots = slots.filter(slot => slot.available);
        
        let slotsHTML = '';
        
        slots.forEach(slot => {
            const isAvailable = slot.available;
            const statusClass = slot.statusClass;
            
            slotsHTML += `
                <div class="col-md-3 col-sm-4 col-6">
                    <div class="time-slot ${slot.status}" 
                         data-time="${slot.time}" 
                         data-end-time="${slot.endTime}"
                         data-price="${slot.price}"
                         data-hour="${slot.hour}"
                         ${isAvailable ? 'onclick="selectTimeSlot(this)"' : ''}
                         style="cursor: ${isAvailable ? 'pointer' : 'not-allowed'};">
                        
                        <div class="fw-bold">${slot.time} - ${slot.endTime}</div>
                        <div class="text-success fw-bold">${slot.price.toLocaleString()}đ</div>
                        <div class="mt-1">
                            <span class="badge bg-${statusClass}">${slot.statusText}</span>
                        </div>
                        
                        ${slot.isPeakHour ? '<div class="badge bg-warning mt-1">Giờ cao điểm</div>' : ''}
                        ${slot.isDiscountHour ? '<div class="badge bg-info mt-1">Giảm giá</div>' : ''}
                    </div>
                </div>
            `;
        });

        container.innerHTML = slotsHTML;
        
        // Reset selection
        bookingState.selectedTime = null;
        document.getElementById('nextToStep3').disabled = true;
    }

    // Time slot selection
    window.selectTimeSlot = function(element) {
        if (element.classList.contains('booked') || element.classList.contains('passed')) {
            return;
        }

        // Remove previous selection
        document.querySelectorAll('.time-slot').forEach(slot => {
            slot.classList.remove('selected');
        });

        // Select this slot
        element.classList.add('selected');

        const selectedTime = element.dataset.time;
        const selectedEndTime = element.dataset.endTime;
        const selectedPrice = parseInt(element.dataset.price);

        bookingState.selectedTime = selectedTime;
        bookingState.totalPrice = selectedPrice * bookingState.selectedDuration;

        // Enable next button
        document.getElementById('nextToStep3').disabled = false;

        console.log('Time slot selected:', selectedTime, 'Price:', bookingState.totalPrice);
    };

    // Payment Selection
    function setupPaymentSelection() {
        document.querySelectorAll('.payment-option').forEach(option => {
            option.addEventListener('click', function() {
                const method = this.dataset.method;
                const radio = this.querySelector('input[type="radio"]');
                
                // Remove previous selection
                document.querySelectorAll('.payment-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                
                // Select this option
                this.classList.add('selected');
                radio.checked = true;
                
                // Add has-selection class to container
                document.querySelector('.payment-methods').classList.add('has-selection');
                
                console.log('Payment method selected:', method);
            });
        });
    }

    // Navigation
    function setupNavigation() {
        document.getElementById('nextToStep2').addEventListener('click', () => {
            if (bookingState.selectedCourt) {
                showStep(2);
                loadTimeSlots();
            }
        });

        document.getElementById('backToStep1').addEventListener('click', () => showStep(1));
        
        document.getElementById('nextToStep3').addEventListener('click', () => {
            if (bookingState.selectedTime) {
                showStep(3);
                updateBookingSummary();
            }
        });

        document.getElementById('backToStep2').addEventListener('click', () => showStep(2));
    }

    // Show step with animation
    function showStep(stepNumber) {
        console.log('Showing step:', stepNumber);
        
        // Update step indicators
        document.querySelectorAll('.step-item').forEach((item, index) => {
            item.classList.remove('active', 'completed');
            if (index + 1 < stepNumber) {
                item.classList.add('completed');
            } else if (index + 1 === stepNumber) {
                item.classList.add('active');
            }
        });
        
        // Hide all steps
        document.querySelectorAll('.booking-step').forEach(step => {
            step.classList.add('d-none');
            step.classList.remove('fade-in', 'slide-in-right');
        });
        
        // Show target step with animation
        const targetStep = document.getElementById(`step${stepNumber}`);
        if (targetStep) {
            setTimeout(() => {
                targetStep.classList.remove('d-none');
                targetStep.classList.add('fade-in');
            }, 150);
        }
        
        currentStep = stepNumber;
    }

    // Update booking summary
    function updateBookingSummary() {
        if (bookingState.selectedCourt && bookingState.selectedDate && bookingState.selectedTime) {
            document.getElementById('summaryCourtName').textContent = bookingState.selectedCourt.name;
            document.getElementById('summaryDate').textContent = new Date(bookingState.selectedDate).toLocaleDateString('vi-VN');
            document.getElementById('summaryTime').textContent = `${bookingState.selectedTime} - ${calculateEndTime()}`;
            document.getElementById('summaryDuration').textContent = `${bookingState.selectedDuration} giờ`;
            document.getElementById('summaryTotal').textContent = `${bookingState.totalPrice.toLocaleString()}đ`;
        }
    }

    // Calculate end time
    function calculateEndTime() {
        if (!bookingState.selectedTime) return '';
        
        const [hours, minutes] = bookingState.selectedTime.split(':').map(Number);
        const endHours = hours + bookingState.selectedDuration;
        return `${endHours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
    }

    // Form submission
    function setupFormSubmission() {
        document.getElementById('confirmBooking').addEventListener('click', function() {
            const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value;
            
            if (!paymentMethod) {
                showAlert('Vui lòng chọn phương thức thanh toán', 'warning');
                return;
            }

            // Validate booking data
            if (!bookingState.selectedCourt || !bookingState.selectedDate || !bookingState.selectedTime) {
                showAlert('Vui lòng chọn đầy đủ thông tin đặt sân', 'warning');
                return;
            }

            // Show loading
            showLoading(true);
            this.disabled = true;

            // Prepare booking data
            const bookingData = {
                court_id: bookingState.selectedCourt.id,
                booking_date: bookingState.selectedDate,
                start_time: bookingState.selectedTime,
                duration: bookingState.selectedDuration,
                payment_method: paymentMethod,
                notes: document.getElementById('bookingNotes')?.value || ''
            };

            console.log('Submitting booking:', bookingData);

            // Send AJAX request
            fetch('book.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(bookingData)
            })
            .then(response => {
                if (response.redirected) {
                    // Payment redirect
                    window.location.href = response.url;
                    return;
                }
                return response.text();
            })
            .then(data => {
                let response;
                try {
                    response = typeof data === 'string' ? JSON.parse(data) : data;
                } catch (e) {
                    response = { success: true, message: 'Đặt sân thành công!' };
                }
                
                if (response.success) {
                    showSuccessModal(response.booking_id || Date.now());
                } else {
                    throw new Error(response.error || 'Có lỗi xảy ra');
                }
            })
            .catch(error => {
                console.error('Booking error:', error);
                showAlert('Có lỗi xảy ra khi đặt sân: ' + error.message, 'danger');
            })
            .finally(() => {
                showLoading(false);
                this.disabled = false;
            });
        });
    }

    // Show success modal
    function showSuccessModal(bookingId) {
        const bookingCode = 'BK' + bookingId.toString().slice(-6);
        
        document.getElementById('modalBookingCode').textContent = bookingCode;
        document.getElementById('modalCourtName').textContent = bookingState.selectedCourt.name;
        document.getElementById('modalDateTime').textContent = 
            `${new Date(bookingState.selectedDate).toLocaleDateString('vi-VN')} - ${bookingState.selectedTime}`;
        document.getElementById('modalTotal').textContent = `${bookingState.totalPrice.toLocaleString()}đ`;

        const modal = new bootstrap.Modal(document.getElementById('successModal'));
        modal.show();
    }

    // Show loading overlay
    function showLoading(show) {
        const overlay = document.getElementById('loadingOverlay');
        if (show) {
            overlay.classList.remove('d-none');
        } else {
            overlay.classList.add('d-none');
        }
    }

    // Alert helper function
    function showAlert(message, type = 'info') {
        // Remove existing alerts
        document.querySelectorAll('.alert.position-fixed').forEach(alert => alert.remove());
        
        const alertHTML = `
            <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <i class="fas fa-${type === 'warning' ? 'exclamation-triangle' : type === 'danger' ? 'times-circle' : 'info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', alertHTML);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            const alert = document.querySelector('.alert.position-fixed');
            if (alert) alert.remove();
        }, 5000);
    }

    console.log('Enhanced booking system initialized');
});