<style>
    .company-header {
        height: 75px;
        background: #151e33;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 30px;
    }

    .company-header-left {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .company-header-title {
        font-size: 18px;
        font-weight: 600;
    }

    .company-header-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .company-header-button {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.15);
        background: transparent;
        color: #ffffff;
        cursor: pointer;
    }
</style>

<header class="company-header">

    <div class="company-header-left">

        <button class="company-header-button" type="button">
            <i class="fas fa-bars"></i>
        </button>

        <div class="company-header-title">
            Yes Trades Company Management
        </div>

    </div>

    <div class="company-header-right">

        <button class="company-header-button" type="button">
            <i class="fas fa-bell"></i>
        </button>

        <button class="company-header-button" type="button">
            <i class="fas fa-moon"></i>
        </button>

      <form method="POST"
      action="{{ route('company.logout') }}"
      onsubmit="return confirm('Are you sure you want to log out?');">

    @csrf

    <button class="company-header-button"
            type="submit"
            title="Logout">

        <i class="fas fa-right-from-bracket"></i>
    </button>

</form>

    </div>

</header>