$(document).ready(function () {
let syncBtn = $('#sync-button');
syncBtn.prop("disabled", true).text("Verificando...");

    setInterval(function () {
        $.get('/sync/status', function (data) {
            const progressWrapper = $('#sync-progress-wrapper');
            const progressBar = $('#sync-progress-bar');
            const syncBtn = $('#sync-button');

            if (data.status === 'rodando') {
                progressWrapper.removeClass('d-none');
                progressBar
                    .removeClass('bg-success bg-danger')
                    .addClass('bg-warning')
                    .css('width', data.progresso + '%')
                    .text('🔄 ' + data.progresso + '%');
                syncBtn.prop("disabled", true).text("🔄 Sincronizando...");
            } else if (data.status === 'concluido') {
                progressWrapper.removeClass('d-none');
                progressBar
                    .removeClass('bg-warning bg-danger')
                    .addClass('bg-success')
                    .css('width', '100%')
                    .text('✅ Concluído');
                syncBtn.prop("disabled", false).text("🔄 Sincronizar Anúncios");

                setTimeout(() => {
                    progressWrapper.fadeOut();
                }, 5000);
            } else if (data.status === 'falhou') {
                progressWrapper.removeClass('d-none');
                progressBar
                    .removeClass('bg-warning bg-success')
                    .addClass('bg-danger')
                    .css('width', '100%')
                    .text('❌ Erro');
                syncBtn.prop("disabled", false).text("🔄 Tentar Novamente");
            } else {
                progressWrapper.fadeOut();
                syncBtn.prop("disabled", false).text("🔄 Sincronizar Anúncios");
            }
        });
    }, 3000);
});
