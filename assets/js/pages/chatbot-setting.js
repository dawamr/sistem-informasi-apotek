/**
 * Chatbot Setting Page JavaScript
 * DataTables initialization and utilities
 */

$(document).ready(function() {
    // Initialize DataTable
    if ($('#chatbot-access-table').length) {
        $('#chatbot-access-table').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang cocok",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            },
            order: [[0, 'asc']],
            pageLength: 25,
            responsive: true
        });
    }
});
