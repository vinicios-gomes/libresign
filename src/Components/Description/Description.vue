<!--
  - @copyright Copyright (c) 2021 Lyseon Tech <contato@lt.coop.br>
  -
  - @author Lyseon Tech <contato@lt.coop.br>
  - @author Vinicios Gomes <viniciusgomesvaian@gmail.com>
  -
  - @license GNU AGPL version 3 or any later version
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program.  If not, see <http://www.gnu.org/licenses/>.
  -
  -->

<template>
	<div class="container">
		<header>
			<img :src="image">
			<p>{{ pdfName }}</p>
			<span>{{ pdfDescription }}</span>
		</header>
		<div id="body">
			<form @submit="e => e.preventDefault()">
				<div v-show="signaturePath" class="form-group">
					<label for="password">Senha da Assinatura</label>
					<div class="form-ib-group">
						<input id="password" v-model="password" type="password">
						<button type="button"
							:value="'Assinar Documento'"
							class="primary"
							:disabled="updating"
							@click="checkAssign">
							Assinar Documento
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</template>

<script>
import { showError, showSuccess } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'
import Image from '../../assets/images/application-pdf.png'
import { generateUrl } from '@nextcloud/router'
import { joinPaths } from '@nextcloud/paths'

export default {
	name: 'Description',

	props: {
		pdfName: {
			type: String,
			required: true,
			default: 'Nome do PDF',
		},
		pdfDescription: {
			type: String,
			required: false,
			default: 'Descrição',
		},
	},

	data() {
		return {
			image: Image,
			updating: false,
			signaturePath: '2',
			password: '',
			asign: true,
		}
	},

	computed: {
		hasSavePossible() {
			return !!this.password
		},
	},

	methods: {

		async sign() {
			this.updating = true
			try {
				const response = await axios.post(generateUrl('/apps/libresign/api/0.1/sign'), {
					inputFilePath: joinPaths(
						this.fileInfo.get('path'), this.fileInfo.get('name')
					),
					outputFolderPath: this.fileInfo.get('path'),
					certificatePath: this.signaturePath,
					password: this.password,
				})
				showSuccess(response)
			} catch (err) {
				showError(err)
			}
		},

		checkAssign() {
			if (this.hasSavePossible === true) {
				showSuccess('Assinado!')
			}
		},
	},
}
</script>

<style lang="scss" scoped>
@import './styles'
</style>
