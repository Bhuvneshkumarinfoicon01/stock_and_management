<script>
$(document).ready(function () {
    function calculatedSellingPrice(cost, markupPercent) {
        const base = parseFloat(cost) || 0;
        const markup = parseFloat(markupPercent) || 0;
        return (base * (1 + (markup / 100))).toFixed(2);
    }

    function rowCost($row) {
        if ($('#product_cost').length) {
            return $('#product_cost').val();
        }

        return $row.data('store-cost') || 0;
    }

    window.refreshStoreMarkupPrices = function refreshStoreMarkupPrices() {
        $('.store-markup-row').each(function () {
            const $row = $(this);
            const markup = $row.find('.store-markup-input').val();
            $row.find('.store-computed-price').val(calculatedSellingPrice(rowCost($row), markup));
        });

        if ($('#primary_selling_price').length) {
            const selectedStoreId = $('select[name="store_id"]').val();
            const $selectedRow = $('.store-markup-row[data-store-id="' + selectedStoreId + '"]');
            const primaryMarkup = $selectedRow.find('.store-markup-input').val() || 0;
            const primaryPrice = calculatedSellingPrice($('#product_cost').val(), primaryMarkup);
            $('#primary_selling_price').val(primaryPrice);
            $('#product_price_hidden').val(primaryPrice);
        }
    };

    function setFollowerMarkupLocked(locked) {
        $('.store-markup-input-follower').each(function () {
            const $input = $(this);
            $input.prop('readonly', locked);
            $input.toggleClass('bg-light', locked);
        });
    }

    function syncFollowerMarkupsFromFirst() {
        const firstMarkup = $('.store-markup-input-first').first().val();
        $('.store-markup-input-follower').val(firstMarkup);
        refreshStoreMarkupPrices();
    }

    function applyUnifiedMarkupState() {
        const unified = $('#unified_store_markup').is(':checked');

        if (!unified) {
            setFollowerMarkupLocked(false);
            return;
        }

        syncFollowerMarkupsFromFirst();
        setFollowerMarkupLocked(true);
    }

    $('#unified_store_markup').on('change', function () {
        if ($(this).is(':checked')) {
            syncFollowerMarkupsFromFirst();
            setFollowerMarkupLocked(true);
        } else {
            setFollowerMarkupLocked(false);
        }
    });

    $(document).on('input change', '.store-markup-input-first', function () {
        if ($('#unified_store_markup').is(':checked')) {
            syncFollowerMarkupsFromFirst();
        } else {
            refreshStoreMarkupPrices();
        }
    });

    $(document).on('input change', '.store-markup-input-follower', function () {
        if ($('#unified_store_markup').is(':checked')) {
            return;
        }
        refreshStoreMarkupPrices();
    });

    $('select[name="store_id"]').on('change', refreshStoreMarkupPrices);

    applyUnifiedMarkupState();
    refreshStoreMarkupPrices();
});
</script>
