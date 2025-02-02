
function nextStep(step) {
    const currentStep = document.querySelector('.step:not(.hidden)');
    currentStep.classList.add('hidden');
    document.getElementById('step' + step).classList.remove('hidden');
}

function prevStep(step) {
    const currentStep = document.querySelector('.step:not(.hidden)');
    currentStep.classList.add('hidden');
    document.getElementById('step' + step).classList.remove('hidden');
}