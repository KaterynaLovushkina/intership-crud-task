const form = document.getElementById('form');

document.querySelectorAll('.js-update-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        // console.log('dd');
        const id = e.currentTarget.dataset.id;
        fetch(`/edit/${id}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json', // Modify the content type if needed
                // Add any other headers you require here
            }
        })
        .then(response => {
            console.log(response);
            //document.querySelector('tbody').innerHTML = ''
                // Handle the response (e.g., show a success message)
                // window.location.reload()
        })
        // .catch(error => {
        //         // Handle errors (e.g., display an error message)
        //         console.error(error);
        // });
})
})

