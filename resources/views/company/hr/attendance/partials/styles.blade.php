<style>
    .attendance-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 24px;
    }

    .attendance-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .attendance-header p {
        margin: 0;
        color: #aab4ca;
    }

    .attendance-add,
    .attendance-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 7px;
        color: #ffffff;
        text-decoration: none;
    }

    .attendance-add {
        background: #2878dc;
    }

    .attendance-back {
        background: #3a4967;
    }

    .attendance-filter-bar {
        display: flex;
        align-items: flex-end;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 20px;
        padding: 18px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .attendance-filter-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .attendance-filter-field label {
        color: #c9d2e5;
        font-size: 12px;
        font-weight: 600;
    }

    .attendance-filter-field input,
    .attendance-filter-field select {
        padding: 9px 11px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
    }

    .attendance-filter-submit {
        min-height: 40px;
        padding: 0 16px;
        border: none;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    .attendance-card {
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .attendance-table-wrapper {
        overflow-x: auto;
    }

    .attendance-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
    }

    .attendance-table th,
    .attendance-table td {
        padding: 13px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
    }

    .attendance-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .attendance-badge {
        display: inline-flex;
        padding: 4px 9px;
        border-radius: 13px;
        font-size: 11px;
    }

    .attendance-badge-in { background: rgba(46,191,114,0.16); color: #59dc96; }
    .attendance-badge-out { background: rgba(255,179,71,0.16); color: #ffc46f; }
    .attendance-badge-manual { background: rgba(170,180,202,0.14); color: #c4ccdc; }

    .attendance-empty {
        padding: 35px;
        text-align: center;
        color: #aab4ca;
    }

    .attendance-form-card {
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .attendance-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
        margin-bottom: 18px;
    }

    .attendance-field label {
        color: #c9d2e5;
        font-size: 13px;
        font-weight: 600;
    }

    .attendance-field input,
    .attendance-field select,
    .attendance-field textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
        box-sizing: border-box;
    }

    .attendance-field textarea {
        resize: vertical;
        min-height: 90px;
    }

    .attendance-error {
        color: #ff8a92;
        font-size: 12px;
    }

    .attendance-help {
        color: #aab4ca;
        font-size: 12px;
        margin-bottom: 18px;
        line-height: 1.6;
    }

    .attendance-form-actions {
        display: flex;
        justify-content: flex-end;
    }

    .attendance-save {
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
</style>
