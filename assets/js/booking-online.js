// Booking Online JavaScript - SIMPLE VERSION
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== Booking page loaded ===');
    
    // Booking state
    let bookingState = {
        selectedCourt: null,
        selectedDate: null,
        selectedTime: null,
        selectedDuration: 1,
        totalPrice: 0
    };

    // Step navigation
    const steps = document.querySelectorAll('.booking-step');
    const stepIndicators = document.querySelectorAll('.step-item');
    let currentStep = 1;

    // Court selection
    document.querySelectorAll('.select-court-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            console.log('Court selected');
            const courtId = this.dataset.courtId;
            const courtName = this.dataset.courtName;
            const courtPrice = parseInt(this.dataset.courtPrice);

            bookingState.selectedCourt = {
                id: courtId,
                name: courtName,
                price: courtPrice
            };

            // Update UI
            document.getElementById('selectedCourtName').textContent = courtName;
            document.getElementById('selectedCourtPrice').textContent = `Giá: ${courtPrice.toLocaleString()}đ/giờ`;

            // Move to step 2 and load time slots
            showStep(2);
            loadTimeSlots();
        });
    });

    // Date selection
    document.getElementById('bookingDate').addEventListener('change', function() {
        bookingState.selectedDate = this.value;
        loadTimeSlots();
    });

    // SIMPLE Time slots loader
    function loadTimeSlots() {
        console.log('Loading time slots...');
        const timeSlotsGrid = document.getElementById('timeSlotsGrid');
        
        if (!timeSlotsGrid) {
            console.error('Time slots grid not found');
            return;
        }

        // Create time slots HTML directly
        let slotsHTML = '';
        
        for (let hour = 6; hour <= 21; hour++) {
            const timeStr = `${hour.toString().padStart(2, '0')}:00`;
            const endTimeStr = `${(hour + 1).toString().padStart(2, '0')}:00`;
            const isAvailable = hour % 2 === 0; // Even hours available
            const price = 120000;
            
            slotsHTML += `
                <div class="time-slot-simple ${isAvailable ? 'available' : 'unavailable'}" 
                     data-time="${timeStr}" 
                     data-price="${price}"
                     onclick="selectTimeSlot(this)"
                     style="
                        border: 2px solid ${isAvailable ? '#28a745' : '#dc3545'};
                        padding: 15px;
                        margin: 5px;
                        border-radius: 8px;
                        text-align: center;
                        cursor: ${isAvailable ? 'pointer' : 'not-allowed'};
                        background: ${isAvailable ? '#f8fff8' : '#fff8f8'};
                        display: inline-block;
                        min-width: 120px;
                     ">
                    <div style="font-weight: bold; font-size: 1.1em;">${timeStr} - ${endTimeStr}</div>
                    <div style="color: #28a745; font-weight: 600;">${price.toLocaleString()}đ</div>
                    <div style="font-size: 0.8em; color: #666;">${isAvailable ? 'Trống' : 'Đã đặt'}</div>
                </div>
            `;
        }

        timeSlotsGrid.innerHTML = slotsHTML;
        console.log('Time slots loaded successfully');
    }

    // Global function to select time slot
    window.selectTimeSlot = function(element) {
        if (element.classList.contains('unavailable')) {
            return;
        }

        // Remove previous selection
        document.querySelectorAll('.time-slot-simple').forEach(slot => {
            const isAvailable = slot.classList.contains('available');
            slot.style.borderColor = isAvailable ? '#28a745' : '#dc3545';
            slot.style.background = isAvailable ? '#f8fff8' : '#fff8f8';
        });

        // Select this slot
        element.style.borderColor = '#0d6efd';
        element.style.background = '#e7f3ff';

        bookingState.selectedTime = element.dataset.time;
        bookingState.totalPrice = parseInt(element.dataset.price);

        // Enable proceed button
        const proceedBtn = document.getElementById('proceedToPayment');
        if (proceedBtn) {
            proceedBtn.disabled = false;
            proceedBtn.classList.remove('btn-secondary');
            proceedBtn.classList.add('btn-primary');
        }

        console.log('Selected time:', element.dataset.time);
    };

    // Step navigation functions
    function showStep(stepNumber) {
        console.log('Showing step:', stepNumber);
        
        // Hide all steps
        steps.forEach(step => step.classList.add('d-none'));
        
        // Show target step
        const targetStep = document.getElementById(`step${stepNumber}`);
        if (targetStep) {
            targetStep.classList.remove('d-none');
        }
        
        // Update step indicators
        stepIndicators.forEach((indicator, index) => {
            indicator.classList.remove('active');
            if (index + 1 <= stepNumber) {
                indicator.classList.add('active');
            }
        });
        
        currentStep = stepNumber;

        // Set default date when entering step 2
        if (stepNumber === 2) {
            const dateInput = document.getElementById('bookingDate');
            if (dateInput && !bookingState.selectedDate) {
                const today = new Date();
                const dateStr = today.toISOString().split('T')[0];
                dateInput.value = dateStr;
                bookingState.selectedDate = dateStr;
            }
        }

        // Update summary if on step 3
        if (stepNumber === 3) {
            updateBookingSummary();
        }
    }

    // Update booking summary
    function updateBookingSummary() {
        if (bookingState.selectedCourt && bookingState.selectedDate && bookingState.selectedTime) {
            document.getElementById('summaryCourtName').textContent = bookingState.selectedCourt.name;
            document.getElementById('summaryDate').textContent = new Date(bookingState.selectedDate).toLocaleDateString('vi-VN');
            document.getElementById('summaryTime').textContent = bookingState.selectedTime;
            document.getElementById('summaryPricePerHour').textContent = `${bookingState.selectedCourt.price.toLocaleString()}đ`;
            document.getElementById('summaryTotal').textContent = `${bookingState.totalPrice.toLocaleString()}đ`;
        }
    }

    // Navigation buttons
    document.getElementById('changeCourtBtn').addEventListener('click', () => showStep(1));
    document.getElementById('backToStep1').addEventListener('click', () => showStep(1));
    document.getElementById('proceedToPayment').addEventListener('click', () => showStep(3));
    document.getElementById('backToStep2').addEventListener('click', () => showStep(2));

    // Confirm booking
    document.getElementById('confirmBooking').addEventListener('click', function() {
        const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value;
        
        if (!paymentMethod) {
            alert('Vui lòng chọn phương thức thanh toán');
            return;
        }

        // Show loading
        this.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Đang xử lý...';
        this.disabled = true;

        // Simulate booking process
        setTimeout(() => {
            const bookingCode = 'BK' + Date.now().toString().slice(-6);
            
            // Update success modal
            document.getElementById('bookingCode').textContent = bookingCode;
            document.getElementById('finalCourtName').textContent = bookingState.selectedCourt.name;
            document.getElementById('finalDateTime').textContent = 
                `${new Date(bookingState.selectedDate).toLocaleDateString('vi-VN')} - ${bookingState.selectedTime}`;
            document.getElementById('finalTotal').textContent = `${bookingState.totalPrice.toLocaleString()}đ`;

            // Show success modal
            const successModal = new bootstrap.Modal(document.getElementById('bookingSuccessModal'));
            successModal.show();

            // Reset button
            this.innerHTML = '<i class="fas fa-check me-2"></i>Xác nhận đặt sân';
            this.disabled = false;
        }, 2000);
    });

    // Initialize
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('bookingDate').value = today;
    bookingState.selectedDate = today;
});

// Simple CSS for time slots
const style = document.createElement('style');
style.textContent = `
    .time-slot-simple {
        transition: all 0.3s ease;
    }
    
    .time-slot-simple:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
`;
document.head.appendChild(style);