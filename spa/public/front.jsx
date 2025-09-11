import React from "react";
import ReactDOM from "react-dom/client";

const Front = () => {
	return (
		<>
			<h2>Hi, this is React</h2>
		</>
	);
};

export default Front;

const root = ReactDOM.createRoot(document.getElementById("csm-app"));
root.render(<Front/>);
