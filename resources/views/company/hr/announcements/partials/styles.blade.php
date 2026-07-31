<style>
    .announcement-form-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .announcement-form-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .announcement-form-header p {
        margin: 0;
        color: #aab4ca;
    }

    .announcement-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 15px;
        border-radius: 7px;
        background: #3a4967;
        color: #ffffff;
        text-decoration: none;
    }

    .announcement-form-card {
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .announcement-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
        margin-bottom: 18px;
    }

    .announcement-field label {
        color: #c9d2e5;
        font-size: 13px;
        font-weight: 600;
    }

    .announcement-field input,
    .announcement-field textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
        box-sizing: border-box;
    }

    .announcement-field textarea {
        resize: vertical;
        min-height: 140px;
    }

    .announcement-checkbox {
        display: inline-flex;
        align-items: center;
        gap: 9px;
    }

    .announcement-checkbox input {
        width: auto;
    }

    .announcement-error {
        color: #ff8a92;
        font-size: 12px;
    }

    .announcement-form-actions {
        display: flex;
        justify-content: flex-end;
    }

    .announcement-save {
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
