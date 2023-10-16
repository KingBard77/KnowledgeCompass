document.addEventListener('DOMContentLoaded', function () {
    const copyButton = document.getElementById('copyButton');
    const textToCopy = document.getElementById('textToCopy');

    let tooltip = new bootstrap.Tooltip(copyButton, {title: "Copy to clipboard"});

    copyButton.addEventListener('click', function () {
        const textarea = document.createElement('textarea');
        textarea.textContent = textToCopy.textContent;

        document.body.appendChild(textarea);
        textarea.select();

        document.execCommand('copy');
        document.body.removeChild(textarea);

        tooltip.dispose();
        tooltip = new bootstrap.Tooltip(copyButton, {title: "Copied!"});
        tooltip.show();

        setTimeout(() => {
            tooltip.dispose();
            tooltip = new bootstrap.Tooltip(copyButton, {title: "Copy to clipboard"});
        }, 2000);
    });
});
