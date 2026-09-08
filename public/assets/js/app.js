document.addEventListener("DOMContentLoaded", function () {
  initSidebarToggle();
  initToasts();
  initConfirmDialog();
  initNotifikasiPolling();
  initTableToolbar();
});

/**
 * Toolbar tabel: dropdown/tanggal langsung submit saat diubah, dan kotak
 * pencarian disubmit otomatis setelah user berhenti mengetik (debounce)
 * supaya terasa seperti live search tanpa perlu library tabel apa pun.
 * Filter tetap diproses server-side lewat query string.
 */
function initTableToolbar() {
  document.querySelectorAll("form[data-auto-search]").forEach(function (form) {
    form.querySelectorAll('select, input[type="date"]').forEach(function (el) {
      el.addEventListener("change", function () {
        form.requestSubmit ? form.requestSubmit() : form.submit();
      });
    });

    var search = form.querySelector('input[type="search"]');
    if (!search) {
      return;
    }

    var awal = search.value;
    var timer = null;

    search.addEventListener("input", function () {
      window.clearTimeout(timer);
      timer = window.setTimeout(function () {
        if (search.value !== awal) {
          form.requestSubmit ? form.requestSubmit() : form.submit();
        }
      }, 450);
    });

    // Enter tidak perlu menunggu debounce.
    search.addEventListener("keydown", function (event) {
      if (event.key === "Enter") {
        window.clearTimeout(timer);
      }
    });
  });
}

function initSidebarToggle() {
  var toggle = document.getElementById("sidebarToggle");
  var sidebar = document.getElementById("sidebar");

  if (!toggle || !sidebar) {
    return;
  }

  toggle.addEventListener("click", function () {
    sidebar.classList.toggle("show");
  });

  document.addEventListener("click", function (event) {
    if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
      sidebar.classList.remove("show");
    }
  });
}

function initToasts() {
  document.querySelectorAll(".toast-stack .toast").forEach(function (el) {
    new bootstrap.Toast(el).show();
  });
}

/**
 * Menggantikan window.confirm() bawaan browser (tidak bisa distyle, terkesan
 * murahan) dengan modal Bootstrap. Elemen <form> atau <a> cukup diberi atribut
 * data-confirm="pesan konfirmasi" — tidak perlu wiring manual per halaman.
 */
function initConfirmDialog() {
  var modalEl = document.getElementById("confirmModal");
  if (!modalEl || typeof bootstrap === "undefined") {
    return;
  }

  var modal = new bootstrap.Modal(modalEl);
  var messageEl = document.getElementById("confirmModalMessage");
  var submitBtn = document.getElementById("confirmModalSubmit");
  var pending = null;

  document.addEventListener("submit", function (event) {
    var form = event.target;
    if (form.dataset.confirmed) {
      return;
    }

    // Cek dulu tombol submit yang benar-benar diklik (event.submitter) — beberapa
    // form (mis. validasi logbook) punya beberapa tombol dengan formaction berbeda,
    // dan hanya sebagian yang butuh konfirmasi.
    var trigger =
      event.submitter && event.submitter.hasAttribute("data-confirm")
        ? event.submitter
        : form.hasAttribute("data-confirm")
          ? form
          : null;

    if (!trigger) {
      return;
    }

    event.preventDefault();
    pending = {
      type: "form",
      form: form,
      formaction: event.submitter
        ? event.submitter.getAttribute("formaction")
        : null,
    };
    messageEl.textContent = trigger.dataset.confirm;
    modal.show();
  });

  document.addEventListener("click", function (event) {
    var link = event.target.closest("a[data-confirm]");
    if (link) {
      event.preventDefault();
      pending = { type: "link", el: link };
      messageEl.textContent = link.dataset.confirm;
      modal.show();
    }
  });

  submitBtn.addEventListener("click", function () {
    modal.hide();
    if (!pending) {
      return;
    }
    if (pending.type === "form") {
      pending.form.dataset.confirmed = "1";
      if (pending.formaction) {
        pending.form.action = pending.formaction;
      }
      pending.form.submit();
    } else {
      window.location.href = pending.el.href;
    }
    pending = null;
  });
}

/**
 * Poll lonceng notifikasi secara berkala supaya notifikasi baru (misalnya hasil
 * validasi logbook oleh guru) muncul tanpa siswa harus me-reload halaman.
 */
function initNotifikasiPolling() {
  var wrapper = document.querySelector("[data-poll-url]");
  var badge = document.getElementById("notifBadge");
  var list = document.getElementById("notifList");
  var bacaSemua = document.getElementById("notifBacaSemua");

  if (!wrapper || !badge || !list) {
    return;
  }

  var pollUrl = wrapper.dataset.pollUrl;
  var intervalMs = 20000;

  function escapeHtml(str) {
    var div = document.createElement("div");
    div.textContent = str == null ? "" : str;
    return div.innerHTML;
  }

  function render(data) {
    var count = data.count || 0;

    badge.textContent = count > 9 ? "9+" : String(count);
    badge.classList.toggle("d-none", count === 0);

    if (bacaSemua) {
      bacaSemua.classList.toggle("d-none", count === 0);
      bacaSemua.href = data.baca_semua_url;
    }

    if (!data.items || data.items.length === 0) {
      list.innerHTML = '<div class="notif-empty">Belum ada notifikasi.</div>';
      return;
    }

    list.innerHTML = data.items
      .map(function (n) {
        return (
          '<a href="' +
          n.baca_url +
          '" class="dropdown-item notif-item' +
          (n.is_read ? "" : " is-unread") +
          '">' +
          '<div class="fw-semibold small">' +
          escapeHtml(n.judul) +
          "</div>" +
          '<div class="text-muted" style="font-size:0.75rem;">' +
          escapeHtml(n.pesan) +
          "</div>" +
          '<div class="text-muted mt-1" style="font-size:0.68rem;">' +
          escapeHtml(n.waktu) +
          "</div>" +
          "</a>"
        );
      })
      .join("");
  }

  function poll() {
    fetch(pollUrl, { headers: { "X-Requested-With": "XMLHttpRequest" } })
      .then(function (res) {
        return res.ok ? res.json() : null;
      })
      .then(function (data) {
        if (data) render(data);
      })
      .catch(function () {
        /* diamkan — biarkan tampilan lama, coba lagi di polling berikutnya */
      });
  }

  setInterval(poll, intervalMs);
}
