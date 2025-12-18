function planningagent(question){
    const responses = {
        'cours lundi' : 'Cours de développement a 10h', 
        'cours mardi' : 'Cours de réseau a 14h', 
        'cours mercredi' : 'Pas de cours', 
        'cours jeudi' : 'Base de données', 
        'cours vendredi' : 'Révision a 15h', 
        };
        const cleaned = question.toLowerCase().trim();
        return responses[cleaned] || 'Information non disponible';
}
console.log(planningagent('cours mardi'));
console.log(planningagent('cours dimanche'));