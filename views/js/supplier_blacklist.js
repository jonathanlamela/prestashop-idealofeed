console.log("Hello word");

$(() => {
    const grid = new window.prestashop.component.Grid('idealofeed_supplier_blacklist');
    grid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());

});
