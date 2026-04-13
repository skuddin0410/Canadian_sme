document.addEventListener('DOMContentLoaded', () => {
  const setStyle = (selector, property, transform = value => value) => {
    document.querySelectorAll(selector).forEach((element) => {
      const attr = selector.match(/\[data-dn-([a-z-]+)\]/)?.[1];
      if (!attr) return;
      const key = attr.replace(/-([a-z])/g, (_, chr) => chr.toUpperCase());
      const value = element.dataset[`dn${key.charAt(0).toUpperCase()}${key.slice(1)}`];
      if (value === undefined || value === '') return;
      element.style[property] = transform(value);
    });
  };

  setStyle('[data-dn-bg]', 'background');
  setStyle('[data-dn-bg]', 'backgroundColor');
  setStyle('[data-dn-color]', 'color');
  setStyle('[data-dn-width]', 'width');
  setStyle('[data-dn-min-height]', 'minHeight');
  setStyle('[data-dn-height]', 'height');
  setStyle('[data-dn-max-height]', 'maxHeight');
  setStyle('[data-dn-max-width]', 'maxWidth');
  setStyle('[data-dn-justify]', 'justifyContent');
  setStyle('[data-dn-align-items]', 'alignItems');
  setStyle('[data-dn-align-self]', 'alignSelf');
  setStyle('[data-dn-text-align]', 'textAlign');
  setStyle('[data-dn-object-fit]', 'objectFit');
  setStyle('[data-dn-flex-basis]', 'flex', value => `0 0 ${value}`);
});
