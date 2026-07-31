<style>
    .jobsheet-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 24px;
    }

    .jobsheet-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .jobsheet-header p {
        margin: 0;
        color: #aab4ca;
    }

    .jobsheet-add,
    .jobsheet-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 7px;
        color: #ffffff;
        text-decoration: none;
    }

    .jobsheet-add {
        background: #2878dc;
    }

    .jobsheet-back {
        background: #3a4967;
    }

    .jobsheet-week-nav {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .jobsheet-week-nav a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 7px;
        background: #1e2a44;
        color: #ffffff;
        text-decoration: none;
    }

    .jobsheet-week-label {
        color: #c9d2e5;
        font-weight: 600;
    }

    .jobsheet-day-card {
        padding: 18px 20px;
        border-radius: 10px;
        background: #1e2a44;
        margin-bottom: 16px;
    }

    .jobsheet-day-title {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #c9d2e5;
    }

    .jobsheet-job-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .jobsheet-job-row:last-child {
        border-bottom: none;
    }

    .jobsheet-job-main a {
        color: #ffffff;
        text-decoration: none;
        font-weight: 600;
    }

    .jobsheet-job-meta {
        color: #aab4ca;
        font-size: 12px;
        margin-top: 3px;
    }

    .jobsheet-empty {
        padding: 30px;
        text-align: center;
        color: #aab4ca;
        background: #1e2a44;
        border-radius: 10px;
    }

    .jobsheet-badge {
        display: inline-flex;
        padding: 4px 9px;
        border-radius: 13px;
        font-size: 11px;
        white-space: nowrap;
    }

    .jobsheet-badge-low { background: rgba(170,180,202,0.14); color: #c4ccdc; }
    .jobsheet-badge-medium { background: rgba(40,120,220,0.16); color: #6fa8ee; }
    .jobsheet-badge-high { background: rgba(255,179,71,0.16); color: #ffc46f; }
    .jobsheet-badge-urgent { background: rgba(255,99,99,0.16); color: #ff8a92; }

    .jobsheet-status-assigned { background: rgba(170,180,202,0.14); color: #c4ccdc; }
    .jobsheet-status-accepted { background: rgba(40,120,220,0.16); color: #6fa8ee; }
    .jobsheet-status-en_route { background: rgba(255,179,71,0.16); color: #ffc46f; }
    .jobsheet-status-on_site { background: rgba(255,179,71,0.16); color: #ffc46f; }
    .jobsheet-status-completed { background: rgba(46,191,114,0.16); color: #59dc96; }
    .jobsheet-status-cancelled { background: rgba(255,99,99,0.16); color: #ff8a92; }

    .jobsheet-form-card {
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .jobsheet-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .jobsheet-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
        margin-bottom: 18px;
    }

    .jobsheet-field-full {
        grid-column: 1 / -1;
    }

    .jobsheet-field label {
        color: #c9d2e5;
        font-size: 13px;
        font-weight: 600;
    }

    .jobsheet-field input,
    .jobsheet-field select,
    .jobsheet-field textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
        box-sizing: border-box;
    }

    .jobsheet-field textarea {
        resize: vertical;
        min-height: 90px;
    }

    .jobsheet-error {
        color: #ff8a92;
        font-size: 12px;
    }

    .jobsheet-form-actions {
        display: flex;
        justify-content: flex-end;
    }

    .jobsheet-save {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 43px;
        padding: 0 20px;
        border: none;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    .jobsheet-delete {
        background: #d24b4b;
        border: none;
        color: #ffffff;
        padding: 0 16px;
        min-height: 42px;
        border-radius: 7px;
        cursor: pointer;
    }

    .jobsheet-detail-row {
        display: flex;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .jobsheet-detail-label {
        width: 180px;
        color: #aab4ca;
        font-size: 13px;
        flex-shrink: 0;
    }

    .jobsheet-detail-value {
        color: #ffffff;
        font-size: 14px;
    }
</style>
