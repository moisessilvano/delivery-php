// Máscaras de valores monetários
function applyMoneyMask(input) {
    let value = input.value.replace(/\D/g, '');
    value = (value / 100).toFixed(2) + '';
    value = value.replace(".", ",");
    value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
    value = value.replace(/(\d)(\d{3}),/g, "$1.$2,");
    input.value = 'R$ ' + value;
}

// Máscara de telefone
function applyPhoneMask(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length <= 10) {
        value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    } else {
        value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    }
    input.value = value;
}

// Máscara de CEP
function applyCepMask(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.replace(/(\d{5})(\d{3})/, '$1-$2');
    input.value = value;
}

// ViaCEP - Buscar endereço pelo CEP
function searchAddressByCep(cep, callback) {
    const cleanCep = cep.replace(/\D/g, '');
    
    if (cleanCep.length !== 8) {
        callback(null);
        return;
    }

    fetch(`https://viacep.com.br/ws/${cleanCep}/json/`)
        .then(response => response.json())
        .then(data => {
            if (data.erro) {
                callback(null);
            } else {
                callback(data);
            }
        })
        .catch(() => callback(null));
}

// Aplicar máscaras automaticamente nos elementos
document.addEventListener('DOMContentLoaded', function() {
    // Máscaras de dinheiro
    document.querySelectorAll('.money-mask').forEach(input => {
        input.addEventListener('input', function() {
            applyMoneyMask(this);
        });
    });

    // Máscaras de telefone
    document.querySelectorAll('.phone-mask').forEach(input => {
        input.addEventListener('input', function() {
            applyPhoneMask(this);
        });
    });

    // Máscaras de CEP com busca automática
    document.querySelectorAll('.cep-mask').forEach(input => {
        input.addEventListener('input', function() {
            applyCepMask(this);
        });

        input.addEventListener('blur', function() {
            const cep = this.value;
            searchAddressByCep(cep, function(address) {
                if (address) {
                    // Preencher campos automaticamente
                    const addressField = document.getElementById('address');
                    const neighborhoodField = document.getElementById('neighborhood');
                    const cityField = document.getElementById('city');
                    const stateField = document.getElementById('state');

                    if (addressField && !addressField.value) {
                        addressField.value = address.logradouro;
                    }
                    if (neighborhoodField && !neighborhoodField.value) {
                        neighborhoodField.value = address.bairro;
                    }
                    if (cityField && !cityField.value) {
                        cityField.value = address.localidade;
                    }
                    if (stateField && !stateField.value) {
                        stateField.value = address.uf;
                    }

                    // Focar no campo número
                    const numberField = document.getElementById('number');
                    if (numberField) {
                        numberField.focus();
                    }
                }
            });
        });
    });
});

// Função para drag and drop de categorias
function initCategoryDragDrop() {
    const tbody = document.querySelector('#categories-table tbody');
    if (!tbody) return;

    let draggedElement = null;

    tbody.addEventListener('dragstart', function(e) {
        if (e.target.tagName === 'TR') {
            draggedElement = e.target;
            e.target.style.opacity = '0.5';
        }
    });

    tbody.addEventListener('dragend', function(e) {
        if (e.target.tagName === 'TR') {
            e.target.style.opacity = '';
            draggedElement = null;
        }
    });

    tbody.addEventListener('dragover', function(e) {
        e.preventDefault();
    });

    tbody.addEventListener('drop', function(e) {
        e.preventDefault();
        const targetRow = e.target.closest('tr');
        
        if (targetRow && draggedElement && targetRow !== draggedElement) {
            const targetIndex = Array.from(tbody.children).indexOf(targetRow);
            const draggedIndex = Array.from(tbody.children).indexOf(draggedElement);
            
            if (targetIndex < draggedIndex) {
                tbody.insertBefore(draggedElement, targetRow);
            } else {
                tbody.insertBefore(draggedElement, targetRow.nextSibling);
            }

            // Atualizar ordem no servidor
            updateCategoryOrder();
            
            // Mostrar notificação
            showNotification('Ordem das categorias atualizada!', 'success');
        }
    });
}

// Atualizar ordem das categorias no servidor
function updateCategoryOrder() {
    const rows = document.querySelectorAll('#categories-table tbody tr');
    const order = Array.from(rows).map((row, index) => ({
        id: row.dataset.categoryId,
        sort_order: index + 1
    }));

    fetch('/categories/update-order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ order: order })
    });
}

// Mostrar notificação discreta
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg shadow-lg text-white transition-all duration-300 transform translate-x-full`;
    
    switch (type) {
        case 'success':
            notification.classList.add('bg-green-500');
            break;
        case 'error':
            notification.classList.add('bg-red-500');
            break;
        case 'warning':
            notification.classList.add('bg-yellow-500');
            break;
        default:
            notification.classList.add('bg-blue-500');
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Remover após 3 segundos
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
