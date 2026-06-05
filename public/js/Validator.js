class Validator 
{
    // Permet de valider un mot de passe
   // Permet de valider un mot de passe
static passwordValidator(controlName, value, lengthWord) {
    // 1. Vérification du champ vide
    if (!value || value.length === 0) {
        return { error: true, message: `Le champ ${controlName} est obligatoire.` };
    }

    // 2. Vérification de la longueur (C'est ici que ça bloquait)
    if (value.length < lengthWord) {
        return { error: true, message: `${controlName} doit contenir au moins ${lengthWord} caractères.` };
    }

    // 3. Si tout est bon, on renvoie error: false
    // IMPORTANT : Ne pas renvoyer " " ou null, mais cet objet précis
    return { error: false, message: "" };
}

    // Permet de valider les adresses email
    static emailValidator(controlName, value) {
        let format = "^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$";
        if (!value.length) {
            return {error: true, message: `${controlName} est obligatoire.`};
        }
        if (!value.match(new RegExp(format))) {
            return {error: true, message: `L' ${controlName} doit respecter le format exemple@gmail.com`};
        }
        return {error: false, message: ""};
    }

     // Permet de valider un numéro de téléphone
    static phoneValidator(controlName, minLength, maxLength, value) {
        let pattern = "^[0-9]+(\.?[0-9]+)?$";
        return !value.length
            ? {error: true, message: `Le champ ${controlName} est obligatoire.`}
            : !value.match(new RegExp(pattern))
            ? {error: true, message: `Le numéro ${controlName} ne doit contenir que des chiffres.`}
            : value.length < minLength || value.length > maxLength
            ? {error: true, message: `Le numéro ${controlName} doit contenir entre ${minLength} et ${maxLength} chiffres.`}
            : null;
    }

      // Permet de valider un nom composé de chaine de caractères et d'espaces
    static nameValidator(controlName, minLength, maxLength, value) {
        let pattern = "^A-Za-aéèçàù -]+$";
       if (!value) {
            return {error: true, message: `Le champ ${controlName} est obligatoire.`};
        }
        if (!value.match(new RegExp(pattern))) {
            return {error: true, message: `Le nom ${controlName} ne doit contenir que des lettres`};
        }
        if (value.length < minLength || value.length > maxLength) {
            return {error: true, message: `Le nom ${controlName} doit contenir entre ${minLength} et ${maxLength} caractères.`};
        }
        if (value.startsWith(" ") || value.endsWith(" ")) {
            return {error: true, message: `Le nom ${controlName} ne doit pas commencer ni terminer par un espace.`};
        }
        return null;
    }
    
      // Permet de valider une adresse
    static addresseValidator(controlName, minLength, maxLength, value) {
        const isContainsnumber = /^(?=.$[0-9]).*$/;
        const isContainsUppercase = /^(?=.[A-Z]).*$/;
        const isContainsLowercase = /^(?=.[a-z]).*$/;
        const iscontainsSymbol = /^(?=.[a-z]).*$/;
       if (!value) {
            return {error: true, message: `Le champ ${controlName} est obligatoire.`};
        }
        if (iscontainsSymbol.test(value) 
            && !isContainsnumber.test(value) 
            && !isContainsUppercase.test(value) 
            && !isContainsLowercase.test(value)) {
            return {error: true, message: `${controlName} ne doit pas contenir que des caractères spéciaux`};
        }
        if (iscontainsSymbol.test(value) 
            && !iscontainsSymbol.test(value) 
            && !isContainsUppercase.test(value) 
            && !isContainsLowercase.test(value)) {
            return {error: true, message: `${controlName} ne doit pas contenir que des chiffres `};
        }
        if (value.length < minLength || value.length > maxLength) {
            return {error: true, message: `Le nom ${controlName} doit contenir entre ${minLength} et ${maxLength} caractères.`};
        }
        if (value.startsWith(" ") || value.endsWith(" ")) {
            return {error: true, message: `Le nom ${controlName} ne doit pas commencer ni terminer par un espace.`};
        }
        return null;
    }

    static nameValidator(controlName, minLength, maxLength, value) {
        let pattern = "^[A-Za-z\é\è\ç\à\ù\ \-]+$";
        if (!value.length) return {error: true, message: `Le champ ${controlName} est obligatoire.`};
        if (!value.match(new RegExp(pattern)))
            return {error: true, message: `Le ${controlName} ne doit contenir que des lettres.`};
        if (value.length < minLength || value.length > maxLength)
            return {error: true, message: `Le ${controlName} doit avoir entre ${minLength} et ${maxLength} caractères.`};
        return {error: false};
    }
}