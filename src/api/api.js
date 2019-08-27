let createRequestInit = (data,newOptions) =>{
	let options = {
		method: 'POST',
        mode: 'cors',
        cache: 'no-cache',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
        },
        redirect: 'follow',
        referrer: 'no-referrer',
        body: JSON.stringify(data),
	}
	if(newOptions){
		for(let keyN in newOptions){
			for(let keyO in options)
			{
				if(keyN == keyO)
				{
					options[keyO] = newOptions[keyN];
					break;
				}
			}
		}
	}
	return options;
}

// .then(response => response.json());

let post = async(url = "", data = {}) => {

	/*utworzenie nagłówków, dodanie danych do wysłania*/
	let requestInit = createRequestInit(data);
	console.log(requestInit);

	/*wysłanie danych i odebranie odpowiedzi*/
	let response = await fetch(url,requestInit);

	/*Jesli nie udało się wysłać*/
	if(!response.ok)
		throw new Error(response.statusText);
	
	/*Konwersja odebranych danych*/
	let res = await response.json();

	/*Jesli wystąpił bład na serwerze*/
	if(res.error)
		throw new Error(res.error);
	
	/*zwroc dane*/
	return res.data;
}




export default { post }