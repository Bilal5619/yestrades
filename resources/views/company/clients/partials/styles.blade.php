<style>
    .client-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 24px;
    }

    .client-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .client-header p {
        margin: 0;
        color: #aab4ca;
    }

    .client-card {
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
        margin-bottom: 20px;
    }

    .client-grid {
        display: grid;
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .client-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .client-field-full {
        grid-column: 1 / -1;
    }

    .client-field label {
        color: #c9d2e5;
        font-size: 13px;
        font-weight: 600;
    }

    .client-field input,
    .client-field select,
    .client-field textarea {
        width: 100%;
        padding: 11px 12px;
        border:
            1px solid
            rgba(255, 255, 255, 0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
        box-sizing: border-box;
    }

    .client-field textarea {
        resize: vertical;
        min-height: 100px;
    }

    .client-error {
        color: #ff8a92;
        font-size: 12px;
    }

    .client-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 22px;
        flex-wrap: wrap;
    }

    .client-save,
    .client-back,
    .client-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 16px;
        border: none;
        border-radius: 7px;
        text-decoration: none;
        cursor: pointer;
        color: #ffffff;
    }

    .client-save,
    .client-button {
        background: #2878dc;
    }

    .client-back {
        background: #3a4967;
    }

    .client-button-secondary {
        background: #3a4967;
    }

    .client-button-danger {
        background: #d24b4b;
    }

    .client-search {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .client-search input,
    .client-search select {
        padding: 10px 12px;
        border:
            1px solid
            rgba(255, 255, 255, 0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
    }

    .client-table {
        width: 100%;
        border-collapse: collapse;
    }

    .client-table th,
    .client-table td {
        padding: 12px 10px;
        border-bottom:
            1px solid
            rgba(255, 255, 255, 0.06);
        text-align: left;
        font-size: 13px;
        vertical-align: top;
    }

    .client-table th {
        color: #aab4ca;
        font-weight: 600;
    }

    .client-table a {
        color: #ffffff;
        text-decoration: none;
        font-weight: 600;
    }

    .client-table small {
        display: block;
        margin-top: 4px;
        color: #aab4ca;
    }

    .client-status {
        display: inline-flex;
        padding: 4px 9px;
        border-radius: 13px;
        font-size: 11px;
        background:
            rgba(46, 191, 114, 0.16);
        color: #59dc96;
    }

    .client-status.inactive {
        background:
            rgba(255, 99, 99, 0.16);
        color: #ff8a92;
    }

    .client-stats {
        display: grid;
        grid-template-columns:
            repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .client-stat {
        padding: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .client-stat span {
        display: block;
        margin-bottom: 8px;
        color: #aab4ca;
        font-size: 12px;
    }

    .client-stat strong {
        font-size: 25px;
    }

    .client-detail-row {
        display: flex;
        gap: 15px;
        padding: 11px 0;
        border-bottom:
            1px solid
            rgba(255, 255, 255, 0.06);
    }

    .client-detail-label {
        width: 190px;
        flex-shrink: 0;
        color: #aab4ca;
        font-size: 13px;
    }

    .client-detail-value {
        color: #ffffff;
        font-size: 14px;
    }

    @media (max-width: 1000px) {
        .client-stats {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .client-grid,
        .client-stats {
            grid-template-columns: 1fr;
        }

        .client-field-full {
            grid-column: auto;
        }

        .client-detail-row {
            flex-direction: column;
        }

        .client-detail-label {
            width: auto;
        }
    }
</style>