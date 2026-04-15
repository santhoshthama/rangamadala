function initAudienceClassPayments() {
  const enrollForms = document.querySelectorAll('.class-enroll-payment-form');
  if (!enrollForms.length) {
    return;
  }

  enrollForms.forEach(function (form) {
    form.addEventListener('submit', function (event) {
      event.preventDefault();

      if (typeof payhere === 'undefined') {
        alert('PayHere is not available right now. Please refresh and try again.');
        return;
      }

      const submitBtn = form.querySelector('button[type="submit"]');
      const classIdInput = form.querySelector('input[name="class_id"]');
      if (!classIdInput || !classIdInput.value) {
        alert('Invalid class selected.');
        return;
      }

      if (submitBtn) {
        submitBtn.disabled = true;
      }

      fetch(form.getAttribute('action'), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'class_id=' + encodeURIComponent(classIdInput.value)
      })
        .then(function (response) {
          return response.json();
        })
        .then(function (data) {
          if (!data.success) {
            alert(data.error || 'Unable to initialize class payment.');
            if (submitBtn) {
              submitBtn.disabled = false;
            }
            return;
          }

          const payment = {
            sandbox: !!data.sandbox,
            merchant_id: data.merchant_id,
            return_url: data.return_url,
            cancel_url: data.cancel_url,
            notify_url: data.notify_url,
            order_id: data.order_id,
            items: data.title || 'Drama Class',
            amount: data.amount,
            currency: 'LKR',
            hash: data.hash,
            first_name: 'Audience',
            last_name: 'User',
            email: 'audience@example.com',
            phone: '0770000000',
            address: 'Sri Lanka',
            city: 'Colombo',
            country: 'Sri Lanka'
          };

          payhere.onCompleted = function () {
            window.location.href = data.return_url;
          };

          payhere.onDismissed = function () {
            if (submitBtn) {
              submitBtn.disabled = false;
            }
          };

          payhere.onError = function (error) {
            alert('Payment error: ' + error);
            if (submitBtn) {
              submitBtn.disabled = false;
            }
          };

          payhere.startPayment(payment);
        })
        .catch(function () {
          alert('Payment initialization failed. Please try again.');
          if (submitBtn) {
            submitBtn.disabled = false;
          }
        });
    });
  });
}

function initAudienceClassesTabs() {
  const classesView = document.getElementById('classes');
  if (!classesView) {
    return;
  }

  const buttons = classesView.querySelectorAll('.classes-subtab-btn');
  const panels = classesView.querySelectorAll('.classes-subtab-panel');

  if (!buttons.length || !panels.length) {
    return;
  }

  initAudienceClassPayments();

  buttons.forEach((button) => {
    button.addEventListener('click', function () {
      const target = button.getAttribute('data-classes-tab');

      buttons.forEach((btn) => {
        const isActive = btn === button;
        btn.classList.toggle('active', isActive);
        btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });

      panels.forEach((panel) => {
        panel.classList.toggle('active', panel.getAttribute('data-classes-panel') === target);
      });
    });
  });
}

function initAudiencePaymentTabs() {
  const paymentsView = document.getElementById('payments');
  if (!paymentsView) {
    return;
  }

  const buttons = paymentsView.querySelectorAll('.payments-subtab-btn');
  const panels = paymentsView.querySelectorAll('.payments-subtab-panel');

  if (!buttons.length || !panels.length) {
    return;
  }

  buttons.forEach((button) => {
    button.addEventListener('click', function () {
      const target = button.getAttribute('data-payment-tab');

      buttons.forEach((btn) => {
        const isActive = btn === button;
        btn.classList.toggle('active', isActive);
        btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });

      panels.forEach((panel) => {
        panel.classList.toggle('active', panel.getAttribute('data-payment-panel') === target);
      });
    });
  });
}

function initMyShowingsFilters() {
  const myShowingsView = document.getElementById('my-showings');
  if (!myShowingsView) {
    return;
  }

  const statusFilter = myShowingsView.querySelector('#myShowingsStatusFilter');
  const searchInput = myShowingsView.querySelector('#myShowingsSearchInput');
  const rows = myShowingsView.querySelectorAll('.my-showings-row');
  const noResults = myShowingsView.querySelector('#myShowingsNoResults');

  if (!statusFilter || !searchInput || !rows.length) {
    return;
  }

  const applyFilters = function () {
    const statusValue = (statusFilter.value || 'all').toLowerCase();
    const searchValue = (searchInput.value || '').trim().toLowerCase();
    let visibleCount = 0;

    rows.forEach((row) => {
      const rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
      const rowPayment = (row.getAttribute('data-payment') || '').toLowerCase();
      const rowSearch = (row.getAttribute('data-search') || '').toLowerCase();

      const matchesStatus =
        statusValue === 'all' ||
        (statusValue === 'paid' && rowPayment === 'paid') ||
        (statusValue === 'pending' && rowStatus === 'pending') ||
        (statusValue === 'rejected' && rowStatus === 'rejected');

      const matchesSearch = searchValue === '' || rowSearch.includes(searchValue);

      const show = matchesStatus && matchesSearch;
      row.style.display = show ? '' : 'none';

      if (show) {
        visibleCount++;
      }
    });

    if (noResults) {
      noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    }
  };

  statusFilter.addEventListener('change', applyFilters);
  searchInput.addEventListener('input', applyFilters);
}

function initOverviewShowingsFilters() {
  const overviewView = document.getElementById('overview');
  if (!overviewView) {
    return;
  }

  const statusFilter = overviewView.querySelector('#overviewShowingsStatusFilter');
  const searchInput = overviewView.querySelector('#overviewShowingsSearchInput');
  const rows = overviewView.querySelectorAll('.overview-showings-row');
  const noResults = overviewView.querySelector('#overviewShowingsNoResults');

  if (!statusFilter || !searchInput || !rows.length) {
    return;
  }

  const applyFilters = function () {
    const statusValue = (statusFilter.value || 'all').toLowerCase();
    const searchValue = (searchInput.value || '').trim().toLowerCase();
    let visibleCount = 0;

    rows.forEach((row) => {
      const rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
      const rowPayment = (row.getAttribute('data-payment') || '').toLowerCase();
      const rowSearch = (row.getAttribute('data-search') || '').toLowerCase();

      const matchesStatus =
        statusValue === 'all' ||
        (statusValue === 'paid' && rowPayment === 'paid') ||
        (statusValue === 'pending' && rowStatus === 'pending') ||
        (statusValue === 'rejected' && rowStatus === 'rejected');

      const matchesSearch = searchValue === '' || rowSearch.includes(searchValue);

      const show = matchesStatus && matchesSearch;
      row.style.display = show ? '' : 'none';

      if (show) {
        visibleCount++;
      }
    });

    if (noResults) {
      noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    }
  };

  statusFilter.addEventListener('change', applyFilters);
  searchInput.addEventListener('input', applyFilters);
}

function closeToast() {
  const toast = document.getElementById('successToast');
  if (toast) {
    toast.style.animation = 'toastSlideOut 0.4s ease forwards';
    setTimeout(() => toast.remove(), 400);
  }
}

window.addEventListener('load', function () {
  initAudienceClassesTabs();
  initAudiencePaymentTabs();
  initOverviewShowingsFilters();
  initMyShowingsFilters();

  const toast = document.getElementById('successToast');
  if (toast) {
    setTimeout(() => {
      closeToast();
    }, 4000);
  }
});
