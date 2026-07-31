<style>
    .shift-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 24px;
    }

    .shift-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .shift-header p {
        margin: 0;
        color: #aab4ca;
    }

    .shift-add,
    .shift-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 7px;
        color: #ffffff;
        text-decoration: none;
    }

    .shift-add {
        background: #2878dc;
    }

    .shift-back {
        background: #3a4967;
    }

    .shift-week-nav {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .shift-week-nav a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 7px;
        background: #1e2a44;
        color: #ffffff;
        text-decoration: none;
    }

    .shift-week-label {
        color: #c9d2e5;
        font-weight: 600;
    }

    .shift-day-card {
        padding: 18px 20px;
        border-radius: 10px;
        background: #1e2a44;
        margin-bottom: 16px;
    }

    .shift-day-title {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #c9d2e5;
    }

    .shift-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .shift-row:last-child {
        border-bottom: none;
    }

    .shift-main a {
        color: #ffffff;
        text-decoration: none;
        font-weight: 600;
    }

    .shift-meta {
        color: #aab4ca;
        font-size: 12px;
        margin-top: 3px;
    }

    .shift-badge {
        display: inline-flex;
        padding: 4px 9px;
        border-radius: 13px;
        font-size: 11px;
        white-space: nowrap;
    }

    .shift-status-scheduled { background: rgba(40,120,220,0.16); color: #6fa8ee; }
    .shift-status-completed { background: rgba(46,191,114,0.16); color: #59dc96; }
    .shift-status-missed { background: rgba(255,99,99,0.16); color: #ff8a92; }
    .shift-status-cancelled { background: rgba(170,180,202,0.14); color: #c4ccdc; }

    .shift-form-card {
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .shift-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .shift-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
        margin-bottom: 18px;
    }

    .shift-field-full {
        grid-column: 1 / -1;
    }

    .shift-field label {
        color: #c9d2e5;
        font-size: 13px;
        font-weight: 600;
    }

    .shift-field input,
    .shift-field select,
    .shift-field textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
        box-sizing: border-box;
    }

    .shift-field textarea {
        resize: vertical;
        min-height: 90px;
    }

    .shift-error {
        color: #ff8a92;
        font-size: 12px;
    }

    .shift-form-actions {
        display: flex;
        justify-content: flex-end;
    }

    .shift-save {
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

    .shift-delete {
        background: #d24b4b;
        border: none;
        color: #ffffff;
        padding: 0 16px;
        min-height: 42px;
        border-radius: 7px;
        cursor: pointer;
    }
</style>
