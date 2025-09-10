/**
 * Cloud Server Management Modal Toggle
 * @param {boolean} show - true to show, false to hide
 */
let csm_modal = (show = true) => {
    const modal = document.getElementById('cloud-server-management-modal');

    if (!modal) return; // Exit if modal not found

    if (show) {
        modal.style.display = 'block';
    } else {
        modal.style.display = 'none';
    }
};