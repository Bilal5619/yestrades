<style>
    .contract-template-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .contract-template-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .contract-template-header p {
        margin: 0;
        color: #aab4ca;
    }

    .contract-template-back {
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

    .contract-template-card {
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .contract-template-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .contract-template-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .contract-template-field-full {
        grid-column: 1 / -1;
    }

    .contract-template-field label {
        color: #c9d2e5;
        font-size: 13px;
        font-weight: 600;
    }

    .contract-template-field input,
    .contract-template-field select,
    .contract-template-field textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
        box-sizing: border-box;
    }

    .contract-template-field textarea {
        resize: vertical;
    }

    .contract-template-checkbox {
        display: inline-flex;
        flex-direction: row;
        align-items: center;
        gap: 9px;
    }

    .contract-template-checkbox input {
        width: auto;
    }

    .contract-template-help {
        color: #aab4ca;
        font-size: 12px;
        line-height: 1.6;
    }

    .contract-template-error {
        color: #ff8a92;
        font-size: 12px;
    }

    .contract-template-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 24px;
    }

    .contract-template-save {
        min-height: 42px;
        padding: 0 18px;
        border: 0;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    .contract-template-version {
        margin-bottom: 20px;
        color: #aab4ca;
    }

    @media (max-width: 750px) {
        .contract-template-grid {
            grid-template-columns: 1fr;
        }

        .contract-template-field-full {
            grid-column: auto;
        }

        .contract-template-header {
            flex-direction: column;
        }
    }
</style>